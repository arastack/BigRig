<?php

namespace App\Http\Controllers;

use App\Models\StudentAttendance;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\TrainingObjective;
use App\Models\TrainingSchedule;
use App\Models\Curriculum;
use App\Models\User;
use App\Models\EnrolmentBatch;
use App\Models\RoleCourse;
use App\Models\StudentInstructorDistribution;

class TrainingScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['when', 'course', 'batch', 'instructor']);
        $when = $filters['when'] ?? 'upcoming';

        $query = TrainingSchedule::with(['instructor', 'course', 'topic', 'batch'])
            ->when($filters['course'] ?? null, fn ($q, $id) => $q->where('course_id', $id))
            ->when($filters['batch'] ?? null, fn ($q, $id) => $q->where('batch_id', $id))
            ->when($filters['instructor'] ?? null, fn ($q, $id) => $q->where('instructor_id', $id));

        match ($when) {
            'today' => $query->whereDate('schedule_date', today()),
            'week' => $query->whereBetween('schedule_date', [today(), today()->addDays(6)]),
            'past' => $query->whereDate('schedule_date', '<', today()),
            'all' => null,
            default => $query->whereDate('schedule_date', '>=', today()),
        };

        $sessions = $query
            ->orderBy('schedule_date', $when === 'past' ? 'desc' : 'asc')
            ->orderBy('time_start')
            ->paginate(30)
            ->withQueryString();

        $attendanceTaken = StudentAttendance::whereIn('schedule_id', $sessions->pluck('id'))
            ->distinct()->pluck('schedule_id')->flip();

        return view('pages/schedule/index', [
            'sessions' => $sessions,
            'filters' => $filters,
            'when' => $when,
            'attendanceTaken' => $attendanceTaken,
            'stats' => [
                'today' => TrainingSchedule::whereDate('schedule_date', today())->count(),
                'week' => TrainingSchedule::whereBetween('schedule_date', [today(), today()->addDays(6)])->count(),
                'upcoming' => TrainingSchedule::whereDate('schedule_date', '>=', today())->count(),
                'pendingChanges' => \App\Models\ScheduleChangeRequest::where('status', 'pending')->count(),
            ],
            'courses' => TrainingObjective::orderBy('objective')->get(),
            'batches' => EnrolmentBatch::orderByDesc('id')->get(),
            'instructors' => $this->instructorQuery()->get(),
        ]);
    }

    /**
     * Form for scheduling one or more sessions for a course.
     */
    public function new(Request $request)
    {
        return view('pages/schedule/create', [
            'courses' => TrainingObjective::orderBy('objective')->get(),
            'batches' => EnrolmentBatch::orderByDesc('active_batch')->orderByDesc('id')->get(),
            'preselect' => [
                'course' => $request->integer('course') ?: null,
                'batch' => EnrolmentBatch::where('active_batch', true)->value('id'),
            ],
        ]);
    }

    private function instructorQuery()
    {
        return User::whereNull('suspended_at')
            ->whereHas('roles', fn ($q) => $q->where('portal', \App\Models\Role::PORTAL_INSTRUCTOR))
            ->orderBy('firstName');
    }

    public function getTopics($id)
    {
        // Fetch topics where objective_id matches the selected course ID
        $topics = Curriculum::where('objective_id', $id)->orderBy('id', 'asc')->get(['id', 'topic']);

        // Return the topics as JSON
        return response()->json($topics);
    }

    public function getInstructors($course_id)
    {
        // Get all role IDs linked to the course
        $roleIds = RoleCourse::where('course_id', $course_id)
            ->pluck('role_id');

        // Fetch users who have any of those roles
        $instructors = $this->instructorQuery()
            ->whereHas('roles', fn ($query) => $query->whereIn('roles.id', $roleIds))
            ->get(['id', 'firstName', 'lastName']);

        // Return as JSON
        return response()->json($instructors);
    }

    public function getInstructorStudents($batch_id, $course_id, $instructor_id)
    {
        // Fetch all student_ids from StudentInstructorDistribution
        $studentIds = StudentInstructorDistribution::where('instructor_id', $instructor_id)
                        ->where('course_id', $course_id)
                        ->where('enrolment_batch_id', $batch_id)
                        ->whereHas('student', fn ($q) => $q->whereNull('suspended_at'))
                        ->pluck('student_id');

        // Fetch students only if student IDs exist
        $students = $studentIds->isNotEmpty()
            ? User::whereIn('id', $studentIds)->orderBy('id', 'asc')->get(['id', 'firstName', 'lastName'])
            : collect(); // Return an empty collection if no students found

        return response()->json($students);
    }

    /**
     * Create one session per time slot. Students are either all placed in every
     * slot, or shared out evenly across the slots (e.g. practical driving groups).
     */
    public function create(Request $request)
    {
        $data = $request->validate([
            'batch_id' => ['required', 'exists:enrolment_batches,id'],
            'course_id' => ['required', 'exists:training_objectives,id'],
            'instructor_id' => ['required', 'exists:users,id'],
            'topic_id' => ['required', 'exists:curriculum,id'],
            'session_type' => ['required', 'in:theory,practical'],
            'allocation' => ['required', 'in:all,split'],
            'students' => ['nullable', 'array'],
            'students.*' => ['integer', 'exists:users,id'],
            'slots' => ['required', 'array', 'min:1', 'max:30'],
            'slots.*.date' => ['required', 'date', 'after_or_equal:today'],
            'slots.*.start' => ['required', 'date_format:H:i'],
            'slots.*.end' => ['required', 'date_format:H:i'],
        ], [
            'slots.*.date.after_or_equal' => 'Sessions cannot be scheduled in the past.',
            'topic_id.required' => 'Choose a curriculum topic. Add topics on the course page first if there are none.',
        ]);

        $instructor = User::findOrFail($data['instructor_id']);

        foreach ($data['slots'] as $n => $slot) {
            if ($slot['end'] <= $slot['start']) {
                return back()->withInput()->withErrors(["slots.{$n}.end" => 'Slot ' . ($n + 1) . ': the end time must be after the start time.']);
            }

            // Overlap with this instructor's other sessions (back-to-back sessions are fine)
            $clash = TrainingSchedule::where('instructor_id', $instructor->id)
                ->whereDate('schedule_date', $slot['date'])
                ->where('time_start', '<', $slot['end'])
                ->where('time_stop', '>', $slot['start'])
                ->first();

            if ($clash) {
                return back()->withInput()->withErrors(["slots.{$n}.start" => sprintf(
                    '%s %s already teaches on %s from %s to %s.',
                    $instructor->firstName, $instructor->lastName,
                    $clash->schedule_date->format('j M Y'),
                    Carbon::parse($clash->time_start)->format('g:ia'),
                    Carbon::parse($clash->time_stop)->format('g:ia')
                )]);
            }
        }

        // Only students actually assigned to this instructor on this course and batch
        $allowed = StudentInstructorDistribution::where('instructor_id', $instructor->id)
            ->where('course_id', $data['course_id'])
            ->where('enrolment_batch_id', $data['batch_id'])
            ->pluck('student_id')->all();
        $students = array_values(array_intersect(array_map('intval', $data['students'] ?? []), $allowed));

        $slots = array_values($data['slots']);
        $groups = $data['allocation'] === 'split' && count($slots) > 1
            ? array_pad(array_chunk($students, (int) max(1, ceil(count($students) / count($slots)))), count($slots), [])
            : array_fill(0, count($slots), $students);

        foreach ($slots as $i => $slot) {
            TrainingSchedule::create([
                'batch_id' => $data['batch_id'],
                'course_id' => $data['course_id'],
                'instructor_id' => $instructor->id,
                'topic_id' => $data['topic_id'],
                'session_type' => $data['session_type'],
                'schedule_date' => $slot['date'],
                'time_start' => $slot['start'],
                'time_stop' => $slot['end'],
                'students' => $groups[$i],
            ]);
        }

        $count = count($slots);

        return redirect()->route('schedule')->with('success', $count === 1 ? 'Session scheduled.' : "{$count} sessions scheduled.");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Edit one training session: when, who teaches it, the topic and who attends.
     */
    public function edit(string $id)
    {
        $schedule = TrainingSchedule::with(['course', 'batch'])->findOrFail($id);

        $studentIds = StudentInstructorDistribution::where('course_id', $schedule->course_id)
            ->when($schedule->batch_id, fn ($q) => $q->where('enrolment_batch_id', $schedule->batch_id))
            ->pluck('student_id')
            ->merge($schedule->studentIds())
            ->unique();

        return view('pages.schedule.edit', [
            'schedule' => $schedule,
            'topics' => Curriculum::where('objective_id', $schedule->course_id)->orderBy('id')->get(),
            'instructors' => User::whereNull('suspended_at')
                ->whereHas('roles', fn ($q) => $q->where('portal', \App\Models\Role::PORTAL_INSTRUCTOR))
                ->orderBy('firstName')->get(),
            'students' => User::whereIn('id', $studentIds)->orderBy('firstName')->get(),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $schedule = TrainingSchedule::findOrFail($id);

        $data = $request->validate([
            'schedule_date' => ['required', 'date'],
            'time_start' => ['required', 'date_format:H:i'],
            'time_stop' => ['required', 'date_format:H:i', 'after:time_start'],
            'instructor_id' => ['required', 'exists:users,id'],
            'topic_id' => ['required', 'exists:curriculum,id'],
            'session_type' => ['required', 'in:theory,practical'],
            'students' => ['nullable', 'array'],
            'students.*' => ['integer', 'exists:users,id'],
        ]);

        // The instructor can't be teaching another session at the same time
        $clash = TrainingSchedule::where('id', '!=', $schedule->id)
            ->where('instructor_id', $data['instructor_id'])
            ->whereDate('schedule_date', $data['schedule_date'])
            ->where('time_start', '<', $data['time_stop'])
            ->where('time_stop', '>', $data['time_start'])
            ->exists();

        if ($clash) {
            return back()->withInput()->with('error', 'That instructor already has a session overlapping this time.');
        }

        $schedule->update([
            'schedule_date' => $data['schedule_date'],
            'time_start' => $data['time_start'],
            'time_stop' => $data['time_stop'],
            'instructor_id' => $data['instructor_id'],
            'topic_id' => $data['topic_id'],
            'session_type' => $data['session_type'],
            'students' => array_map('intval', $data['students'] ?? []),
        ]);

        return redirect()->route('schedule')->with('success', 'Training session updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $schedule = TrainingSchedule::findOrFail($id);

        if (StudentAttendance::where('schedule_id', $schedule->id)->exists()) {
            return redirect()->back()->with('error', 'This session already has attendance recorded and cannot be deleted.');
        }

        $schedule->delete();

        return redirect()->back()->with('success', 'Training session deleted.');
    }
}
