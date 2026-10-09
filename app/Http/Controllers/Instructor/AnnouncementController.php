<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcement;
use App\Models\TrainingObjective;
use App\Models\EnrolmentBatch;
use App\Models\StudentInstructorDistribution;
use App\Models\AnnouncementRead;
use Carbon\Carbon;
use DB;

class AnnouncementController extends Controller
{
    /**
     * Display announcements overview
     */
    public function index()
    {
        $instructor = Auth::user();

        // Get announcements created by this instructor
        $announcements = Announcement::where('created_by', $instructor->id)
            ->with(['course', 'batch'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get statistics
        $stats = [
            'total_announcements' => Announcement::where('created_by', $instructor->id)->count(),
            'active_announcements' => Announcement::where('created_by', $instructor->id)
                ->where('is_active', true)
                ->where(function($query) {
                    $query->whereNull('expiry_date')
                        ->orWhere('expiry_date', '>=', now());
                })
                ->count(),
            'urgent_announcements' => Announcement::where('created_by', $instructor->id)
                ->where('priority', 'high')
                ->where('is_active', true)
                ->count(),
            'total_reads' => AnnouncementRead::whereIn('announcement_id',
                Announcement::where('created_by', $instructor->id)->pluck('id')
            )->count(),
        ];

        // Get recent announcements
        $recentAnnouncements = Announcement::where('created_by', $instructor->id)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('instructor.announcements.index', compact('announcements', 'stats', 'recentAnnouncements'));
    }

    /**
     * Show create announcement form
     */
    public function create()
    {
        $instructor = Auth::user();

        // Get courses assigned to this instructor
        $assignedCourseIds = StudentInstructorDistribution::where('instructor_id', $instructor->id)
            ->pluck('course_id')
            ->unique();

        $courses = TrainingObjective::whereIn('id', $assignedCourseIds)->get();

        // Get batches that have students under this instructor
        $batches = EnrolmentBatch::whereIn('id',
            StudentInstructorDistribution::where('instructor_id', $instructor->id)
                ->join('users', 'student_instructor_distributions.student_id', '=', 'users.id')
                ->pluck('users.enrolment_batch_id')
                ->unique()
        )->get();

        return view('instructor.announcements.create', compact('courses', 'batches'));
    }

    /**
     * Store new announcement
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:general,course,batch,urgent',
            'priority' => 'required|in:low,medium,high',
            'course_id' => 'nullable|exists:training_objectives,id',
            'batch_id' => 'nullable|exists:enrolment_batches,id',
            'publish_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:publish_date',
        ]);

        $instructor = Auth::user();

        // School-wide announcements (no course) need the manage_announcements permission
        if (! $request->course_id && ! $instructor->hasPermission('manage_announcements')) {
            return redirect()->back()
                ->with('error', 'Please choose one of your courses for this announcement.')
                ->withInput();
        }

        // Verify instructor has access to the selected course
        if ($request->course_id) {
            $hasAccess = StudentInstructorDistribution::where('instructor_id', $instructor->id)
                ->where('course_id', $request->course_id)
                ->exists();

            if (!$hasAccess) {
                return redirect()->back()
                    ->with('error', 'You do not have access to create announcements for this course.')
                    ->withInput();
            }
        }

        // Create announcement
        $announcement = Announcement::create([
            'title' => $request->title,
            'content' => $request->content,
            'type' => $request->type,
            'priority' => $request->priority,
            'course_id' => $request->course_id,
            'batch_id' => $request->batch_id,
            'created_by' => $instructor->id,
            'is_active' => true,
            'publish_date' => $request->publish_date ? Carbon::parse($request->publish_date) : now(),
            'expiry_date' => $request->expiry_date ? Carbon::parse($request->expiry_date) : null,
        ]);

        // Send notification to students (optional - implement if needed)
        $this->notifyStudents($announcement);

        return redirect()->route('instructor.announcements')
            ->with('success', 'Announcement created successfully!');
    }

    /**
     * Show announcement details
     */
    public function show($id)
    {
        $instructor = Auth::user();
        $announcement = Announcement::where('created_by', $instructor->id)
            ->with(['course', 'batch'])
            ->findOrFail($id);

        // Get read statistics
        $readStats = [
            'total_reads' => $announcement->reads()->count(),
            'unique_readers' => $announcement->reads()->distinct('user_id')->count('user_id'),
            'recent_reads' => $announcement->reads()
                ->with('user')
                ->orderBy('read_at', 'desc')
                ->take(10)
                ->get(),
        ];

        // Get target audience count
        $targetStudents = $this->getTargetStudentsCount($announcement);

        return view('instructor.announcements.show', compact('announcement', 'readStats', 'targetStudents'));
    }

    /**
     * Show edit announcement form
     */
    public function edit($id)
    {
        $instructor = Auth::user();
        $announcement = Announcement::where('created_by', $instructor->id)->findOrFail($id);

        // Get courses and batches
        $assignedCourseIds = StudentInstructorDistribution::where('instructor_id', $instructor->id)
            ->pluck('course_id')
            ->unique();

        $courses = TrainingObjective::whereIn('id', $assignedCourseIds)->get();

        $batches = EnrolmentBatch::whereIn('id',
            StudentInstructorDistribution::where('instructor_id', $instructor->id)
                ->join('users', 'student_instructor_distributions.student_id', '=', 'users.id')
                ->pluck('users.enrolment_batch_id')
                ->unique()
        )->get();

        return view('instructor.announcements.edit', compact('announcement', 'courses', 'batches'));
    }

    /**
     * Update announcement
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:general,course,batch,urgent',
            'priority' => 'required|in:low,medium,high',
            'course_id' => 'nullable|exists:training_objectives,id',
            'batch_id' => 'nullable|exists:enrolment_batches,id',
            'publish_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:publish_date',
            'is_active' => 'boolean',
        ]);

        $instructor = Auth::user();
        $announcement = Announcement::where('created_by', $instructor->id)->findOrFail($id);

        $announcement->update([
            'title' => $request->title,
            'content' => $request->content,
            'type' => $request->type,
            'priority' => $request->priority,
            'course_id' => $request->course_id,
            'batch_id' => $request->batch_id,
            'is_active' => $request->has('is_active'),
            'publish_date' => $request->publish_date ? Carbon::parse($request->publish_date) : $announcement->publish_date,
            'expiry_date' => $request->expiry_date ? Carbon::parse($request->expiry_date) : null,
        ]);

        return redirect()->route('instructor.announcements')
            ->with('success', 'Announcement updated successfully!');
    }

    /**
     * Delete announcement
     */
    public function destroy($id)
    {
        $instructor = Auth::user();
        $announcement = Announcement::where('created_by', $instructor->id)->findOrFail($id);

        // Delete read records
        $announcement->reads()->delete();

        // Delete announcement
        $announcement->delete();

        return redirect()->route('instructor.announcements')
            ->with('success', 'Announcement deleted successfully!');
    }

    /**
     * Toggle announcement active status
     */
    public function toggle($id)
    {
        $instructor = Auth::user();
        $announcement = Announcement::where('created_by', $instructor->id)->findOrFail($id);

        $announcement->is_active = !$announcement->is_active;
        $announcement->save();

        return redirect()->back()
            ->with('success', 'Announcement status updated successfully!');
    }

    /**
     * Get target students count for an announcement
     */
    private function getTargetStudentsCount($announcement)
    {
        $query = StudentInstructorDistribution::where('instructor_id', $announcement->created_by);

        if ($announcement->course_id) {
            $query->where('course_id', $announcement->course_id);
        }

        $studentIds = $query->pluck('student_id')->unique();

        if ($announcement->batch_id) {
            $studentIds = $studentIds->intersect(
                \App\Models\User::where('enrolment_batch_id', $announcement->batch_id)
                    ->pluck('id')
            );
        }

        return $studentIds->count();
    }

    /**
     * Notify students about new announcement (placeholder for notification system)
     */
    private function notifyStudents($announcement)
    {
        // Implement email/SMS notifications here if needed
        // For now, this is a placeholder
    }
}
