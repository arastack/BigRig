<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Subscription;
use App\Models\StudentAttendance;
use App\Models\TrainingSchedule;
use App\Models\StudentInstructorDistribution;
use App\Models\StudentProgress;
use Carbon\Carbon;

class StudentProgressController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get student's enrolled courses
        $subscription = Subscription::where('user_id', $user->id)
            ->where('payment_status', 'completed')
            ->latest()
            ->first();

        $enrolledCourses = collect();
        if ($subscription) {
            $enrolledCourses = $subscription->objectives;
        }

        // Get progress for each course
        $coursesProgress = $enrolledCourses->map(function($course) use ($user) {
            // Get instructor assignment
            $instructorAssignment = StudentInstructorDistribution::where('student_id', $user->id)
                ->where('course_id', $course->id)
                ->with('instructor')
                ->first();

            // Get all schedules for this course
            $totalSchedules = 0;
            $completedSchedules = 0;

            if ($instructorAssignment) {
                $allSchedules = TrainingSchedule::where('instructor_id', $instructorAssignment->instructor_id)
                    ->where('course_id', $course->id)
                    ->where('batch_id', $user->enrolment_batch_id)
                    ->get();

                $totalSchedules = $allSchedules->count();
                $completedSchedules = $allSchedules->filter(function($schedule) {
                    $schedule_date = Carbon::parse($schedule->schedule_date)->format('Y-m-d');
                    return Carbon::parse($schedule_date . ' ' . $schedule->time_stop)->isPast();
                })->count();
            }

            // Get attendance for this course
            $attendance = StudentAttendance::where('student_id', $user->id)
                ->where('course_id', $course->id)
                ->selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present,
                    SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late
                ')
                ->first();

            $attendanceRate = $attendance->total > 0
                ? round((($attendance->present + $attendance->late) / $attendance->total) * 100)
                : 0;

            // Calculate overall progress
            $overallProgress = $totalSchedules > 0
                ? round(($completedSchedules / $totalSchedules) * 100)
                : 0;

            // Get theory and practical breakdown
            $theorySchedules = TrainingSchedule::where('instructor_id', $instructorAssignment->instructor_id ?? 0)
                ->where('course_id', $course->id)
                ->where('batch_id', $user->enrolment_batch_id)
                ->where('session_type', 'theory')
                ->get();

            $practicalSchedules = TrainingSchedule::where('instructor_id', $instructorAssignment->instructor_id ?? 0)
                ->where('course_id', $course->id)
                ->where('batch_id', $user->enrolment_batch_id)
                ->where('session_type', 'practical')
                ->get();

            $theoryCompleted = $theorySchedules->filter(function($schedule) {
                $schedule_date = Carbon::parse($schedule->schedule_date)->format('Y-m-d');
                return Carbon::parse($schedule_date . ' ' . $schedule->time_stop)->isPast();
            })->count();

            $practicalCompleted = $practicalSchedules->filter(function($schedule) {
                $schedule_date = Carbon::parse($schedule->schedule_date)->format('Y-m-d');
                return Carbon::parse($schedule_date . ' ' . $schedule->time_stop)->isPast();
            })->count();

            return [
                'course' => $course,
                'instructor' => $instructorAssignment ? $instructorAssignment->instructor : null,
                'overall_progress' => $overallProgress,
                'attendance_rate' => $attendanceRate,
                'theory_total' => $theorySchedules->count(),
                'theory_completed' => $theoryCompleted,
                'practical_total' => $practicalSchedules->count(),
                'practical_completed' => $practicalCompleted,
                'classes_completed' => $completedSchedules,
                'classes_total' => $totalSchedules,
            ];
        });

        // Calculate overall statistics
        $overallStats = [
            'total_courses' => $coursesProgress->count(),
            'average_progress' => $coursesProgress->avg('overall_progress'),
            'average_attendance' => $coursesProgress->avg('attendance_rate'),
            'total_classes' => $coursesProgress->sum('classes_total'),
            'completed_classes' => $coursesProgress->sum('classes_completed'),
        ];

        return view('student.progress', compact('coursesProgress', 'overallStats'));
    }

    public function course($courseId)
    {
        $user = Auth::user();

        $enrolledCourseIds = Subscription::where('user_id', $user->id)
            ->where('payment_status', 'completed')
            ->get()
            ->flatMap(fn ($subscription) => $subscription->objectiveIds())
            ->all();

        abort_unless(in_array((int) $courseId, $enrolledCourseIds, true), 403);

        $course = \App\Models\TrainingObjective::with('curriculum')->findOrFail($courseId);

        $assignment = StudentInstructorDistribution::with('instructor')
            ->where('student_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        $sessions = TrainingSchedule::with(['topic', 'instructor'])
            ->where('course_id', $course->id)
            ->where('batch_id', $user->enrolment_batch_id)
            ->orderBy('schedule_date')
            ->orderBy('time_start')
            ->get()
            ->filter(fn ($session) => $session->studentIds() === [] || $session->hasStudent($user->id))
            ->values();

        $attendance = StudentAttendance::where('student_id', $user->id)
            ->whereIn('schedule_id', $sessions->pluck('id'))
            ->get()
            ->keyBy('schedule_id');

        $isPast = fn ($session) => Carbon::parse($session->schedule_date->format('Y-m-d') . ' ' . $session->time_stop)->isPast();
        $coveredTopicIds = $sessions->filter($isPast)->pluck('topic_id')->filter()->unique();

        $attended = $attendance->whereIn('status', ['present', 'late'])->count();

        return view('student.course-progress', [
            'course' => $course,
            'instructor' => $assignment?->instructor,
            'sessions' => $sessions,
            'attendance' => $attendance,
            'isPast' => $isPast,
            'coveredTopicIds' => $coveredTopicIds,
            'stats' => [
                'completed' => $sessions->filter($isPast)->count(),
                'total' => $sessions->count(),
                'attended' => $attended,
                'recorded' => $attendance->count(),
                'topics_covered' => $course->curriculum->whereIn('id', $coveredTopicIds)->count(),
                'topics_total' => $course->curriculum->count(),
            ],
        ]);
    }
}
