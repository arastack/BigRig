<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcement;
use App\Models\StudentAttendance;
use App\Models\TrainingSchedule;
use App\Models\Subscription;
use App\Models\CourseMaterial;
use App\Models\StudentInstructorDistribution;
use App\Models\TrainingObjective;
use Carbon\Carbon;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get student's active subscription
        $subscription = Subscription::where('user_id', $user->id)
            ->where('payment_status', 'completed')
            ->latest()
            ->first();

        if (!$subscription) {
            // Redirect to checkout/pay if payment is still pending
            return redirect('/checkout/pay');
        }

        // Get enrolled courses from the subscription
        $enrolledCourses = collect();
        if ($subscription) {
            // Use the existing getObjectivesAttribute accessor
            $enrolledCourses = $subscription->objectives;
        }

        // Get all instructor assignments for this student
        $instructorAssignments = StudentInstructorDistribution::where('student_id', $user->id)
            ->with(['instructor', 'course'])
            ->get()
            ->keyBy('course_id'); // Key by course_id for easy lookup

        // Get upcoming schedules for all assigned instructors
        $upcomingSchedule = collect();

        if ($instructorAssignments->isNotEmpty()) {
            $instructorIds = $instructorAssignments->pluck('instructor_id')->unique();
            $courseIds = $instructorAssignments->pluck('course_id');

            $upcomingSchedule = TrainingSchedule::whereIn('instructor_id', $instructorIds)
                ->whereIn('course_id', $courseIds)
                ->where('batch_id', $user->enrolment_batch_id)
                ->whereBetween('schedule_date', [Carbon::today(), Carbon::today()->addDays(14)])
                ->orderBy('schedule_date', 'asc')
                ->orderBy('time_start', 'asc')
                ->with(['topic', 'course', 'instructor'])
                ->get()
                // Only sessions this student is booked on
                ->filter(fn ($session) => $session->studentIds() === [] || $session->hasStudent($user->id))
                ->take(6)
                ->values();
        }

        // Get course materials for all enrolled courses
        $courseMaterials = collect();
        if ($enrolledCourses->isNotEmpty()) {
            $courseIds = $enrolledCourses->pluck('id');
            $courseMaterials = CourseMaterial::whereIn('objective_id', $courseIds)
                ->latest()
                ->take(10)
                ->get();

            // Add course name to each material for clarity
            $courseMaterials = $courseMaterials->map(function($material) use ($enrolledCourses) {
                $material->course_name = $enrolledCourses->where('id', $material->objective_id)->first()->objective ?? 'Unknown Course';
                return $material;
            });
        }

        // Prepare course-specific data for the view
        $courseData = $enrolledCourses->map(function($course) use ($instructorAssignments, $user) {
            $assignment = $instructorAssignments->get($course->id);

            return [
                'course' => $course,
                'instructor' => $assignment ? $assignment->instructor : null,
                'hasInstructor' => !is_null($assignment),
                'theory_hours' => $course->theory_session,
                'practical_hours' => $course->practical_session,
            ];
        });

        // Get attendance statistics
        $attendanceStats = StudentAttendance::where('student_id', $user->id)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN status = "excused" THEN 1 ELSE 0 END) as excused
            ')
            ->first();

        $attendancePercentage = $attendanceStats->total > 0
            ? round((($attendanceStats->present + $attendanceStats->late) / $attendanceStats->total) * 100)
            : 0;

        // Latest announcements addressed to this student
        $announcements = Announcement::visibleTo($user)
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Mark which announcements are unread
        $announcements = $announcements->map(function($announcement) use ($user) {
            $announcement->is_read = $announcement->isReadBy($user->id);
            return $announcement;
        });

        $unreadCount = $announcements->where('is_read', false)->count();

        $progress = \App\Support\ApplicationProgress::for($user);

        return view('student.dashboard', compact(
            'progress',
            'subscription',
            'enrolledCourses',
            'instructorAssignments',
            'upcomingSchedule',
            'courseMaterials',
            'courseData',
            'attendanceStats',
            'attendancePercentage',
            'announcements',
            'unreadCount',
        ));
    }
}
