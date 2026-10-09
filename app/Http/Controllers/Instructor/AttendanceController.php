<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\EnrolmentBatch;
use App\Models\TrainingObjective;
use App\Models\TrainingSchedule;
use App\Models\StudentAttendance;
use App\Models\StudentInstructorDistribution;
use App\Models\User;
use Carbon\Carbon;
use DB;

class AttendanceController extends Controller
{
    /**
     * Display attendance overview
     */
    public function index()
    {
        $instructor = Auth::user();

        // Get schedules that need attendance marking
        $pendingSchedules = TrainingSchedule::where('instructor_id', $instructor->id)
            ->whereDate('schedule_date', '<=', Carbon::today())
            ->with(['course', 'topic', 'batch'])
            ->orderBy('schedule_date', 'desc')
            ->orderBy('time_start', 'desc')
            ->get()
            ->filter(function($schedule) {
                // Check if attendance has been marked for this schedule
                $studentsInClass = StudentInstructorDistribution::where('instructor_id', $schedule->instructor_id)
                    ->where('course_id', $schedule->course_id)
                    ->count();

                $attendanceMarked = StudentAttendance::where('schedule_id', $schedule->id)->count();

                return $attendanceMarked < $studentsInClass;
            });

        // Get recent attendance records
        $recentAttendance = TrainingSchedule::where('instructor_id', $instructor->id)
            ->whereHas('attendances')
            ->with(['course', 'topic', 'batch', 'attendances'])
            ->orderBy('schedule_date', 'desc')
            ->take(10)
            ->get();

        // Get attendance statistics for the month
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $monthlyStats = DB::table('training_schedules')
            ->join('student_attendance', 'training_schedules.id', '=', 'student_attendance.schedule_id')
            ->where('training_schedules.instructor_id', $instructor->id)
            ->whereBetween('training_schedules.schedule_date', [$monthStart, $monthEnd])
            ->select(
                DB::raw('COUNT(DISTINCT student_attendance.id) as total_records'),
                DB::raw('SUM(CASE WHEN student_attendance.status = "present" THEN 1 ELSE 0 END) as present'),
                DB::raw('SUM(CASE WHEN student_attendance.status = "absent" THEN 1 ELSE 0 END) as absent'),
                DB::raw('SUM(CASE WHEN student_attendance.status = "late" THEN 1 ELSE 0 END) as late'),
                DB::raw('SUM(CASE WHEN student_attendance.status = "excused" THEN 1 ELSE 0 END) as excused')
            )
            ->first();

        return view('instructor.attendance.index', compact('pendingSchedules', 'recentAttendance', 'monthlyStats'));
    }

    /**
     * Show attendance marking form for a specific schedule
     */
    public function mark($scheduleId)
    {
        $instructor = Auth::user();

        // Get the schedule
        $schedule = TrainingSchedule::where('id', $scheduleId)
            ->where('instructor_id', $instructor->id)
            ->with(['course', 'topic', 'batch'])
            ->firstOrFail();

        // Get students enrolled in this course under this instructor
        $students = StudentInstructorDistribution::where('instructor_id', $instructor->id)
            ->where('course_id', $schedule->course_id)
            ->with('student')
            ->get()
            ->map(function($distribution) use ($schedule) {
                $student = $distribution->student;

                // Check if attendance already marked
                $existingAttendance = StudentAttendance::where('student_id', $student->id)
                    ->where('schedule_id', $schedule->id)
                    ->first();

                $student->attendance = $existingAttendance;
                $student->status = $existingAttendance ? $existingAttendance->status : null;

                return $student;
            });

        // Get attendance summary for this schedule
        $attendanceSummary = StudentAttendance::where('schedule_id', $schedule->id)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN status = "excused" THEN 1 ELSE 0 END) as excused
            ')
            ->first();

        return view('instructor.attendance.mark', compact('schedule', 'students', 'attendanceSummary'));
    }

    /**
     * Save attendance records
     */
    public function save(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:training_schedules,id',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:users,id',
            'attendance.*.status' => 'required|in:present,absent,late,excused',
            'attendance.*.notes' => 'nullable|string|max:255'
        ]);

        $instructor = Auth::user();
        $schedule = TrainingSchedule::findOrFail($request->schedule_id);

        // Verify instructor has access to this schedule
        if ($schedule->instructor_id != $instructor->id) {
            abort(403, 'Unauthorized access to this schedule.');
        }

        // Only students this instructor teaches on this course can be marked
        $allowedStudentIds = StudentInstructorDistribution::where('instructor_id', $instructor->id)
            ->where('course_id', $schedule->course_id)
            ->pluck('student_id')
            ->all();
        $submittedIds = collect($request->attendance)->pluck('student_id')->map(fn ($id) => (int) $id);
        if ($submittedIds->diff($allowedStudentIds)->isNotEmpty()) {
            abort(403, 'Attendance can only be recorded for your own students.');
        }

        DB::beginTransaction();
        try {
            foreach ($request->attendance as $record) {
                StudentAttendance::updateOrCreate(
                    [
                        'student_id' => $record['student_id'],
                        'schedule_id' => $schedule->id,
                    ],
                    [
                        'course_id' => $schedule->course_id,
                        'status' => $record['status'],
                        'notes' => $record['notes'] ?? null,
                        'marked_by' => $instructor->id,
                        'check_in_time' => $record['status'] != 'absent' ? now()->format('H:i:s') : null,
                    ]
                );
            }

            DB::commit();

            return redirect()->route('instructor.attendance')
                ->with('success', 'Attendance has been marked successfully.');

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()
                ->with('error', 'Failed to save attendance. Please try again.')
                ->withInput();
        }
    }

    /**
     * Edit attendance for a specific class
     */
    public function edit($scheduleId)
    {
        return $this->mark($scheduleId);
    }

    /**
     * Get attendance report for a course
     */
    /**
     * Generate attendance report
     */
    public function report(Request $request)
    {
        $instructor = Auth::user();

        // Get filter parameters
        $courseId = $request->get('course_id');
        $batchId = $request->get('batch_id');
        $dateFrom = $request->get('date_from', Carbon::now()->subMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        // Get courses assigned to instructor
        $courses = TrainingObjective::whereIn('id',
            StudentInstructorDistribution::where('instructor_id', $instructor->id)
                ->pluck('course_id')
                ->unique()
        )->get();

        // Get batches
        $batches = EnrolmentBatch::whereIn('id',
            User::whereIn('id',
                StudentInstructorDistribution::where('instructor_id', $instructor->id)
                    ->pluck('student_id')
            )->pluck('enrolment_batch_id')->unique()
        )->get();

        // Get students based on filters
        $studentsQuery = StudentInstructorDistribution::where('instructor_id', $instructor->id)
            ->with('student');

        if ($courseId) {
            $studentsQuery->where('course_id', $courseId);
        }

        $students = $studentsQuery->get()->pluck('student')->unique('id');

        if ($batchId) {
            $students = $students->where('enrolment_batch_id', $batchId);
        }

        // Generate report data for each student
        $studentReports = [];
        foreach ($students as $student) {
            $attendanceQuery = StudentAttendance::where('student_id', $student->id)
                ->whereBetween('created_at', [$dateFrom, $dateTo]);

            if ($courseId) {
                $attendanceQuery->where('course_id', $courseId);
            }

            $attendanceRecords = $attendanceQuery->get();

            $totalClasses = $attendanceRecords->count();
            $present = $attendanceRecords->where('status', 'present')->count();
            $absent = $attendanceRecords->where('status', 'absent')->count();
            $late = $attendanceRecords->where('status', 'late')->count();
            $excused = $attendanceRecords->where('status', 'excused')->count();

            $attendanceRate = $totalClasses > 0
                ? round((($present + $late) / $totalClasses) * 100)
                : 0;

            $studentReports[] = [
                'student' => $student,
                'course' => $courseId ? TrainingObjective::find($courseId) : null,
                'total_classes' => $totalClasses,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'excused' => $excused,
                'attendance_rate' => $attendanceRate
            ];
        }

        // Calculate overall statistics
        $stats = [
            'total_sessions' => collect($studentReports)->sum('total_classes'),
            'total_present' => collect($studentReports)->sum('present'),
            'total_absent' => collect($studentReports)->sum('absent'),
            'total_late' => collect($studentReports)->sum('late'),
            'total_excused' => collect($studentReports)->sum('excused'),
            'average_attendance' => collect($studentReports)->avg('attendance_rate') ?? 0,
        ];

        // Prepare chart data
        $chartData = $this->prepareChartData($instructor->id, $courseId, $dateFrom, $dateTo);

        return view('instructor.attendance.report', compact(
            'studentReports',
            'stats',
            'courses',
            'batches',
            'chartData'
        ));
    }

    /**
     * Prepare chart data for visualization
     */
    private function prepareChartData($instructorId, $courseId, $dateFrom, $dateTo)
    {
        $dates = [];
        $rates = [];

        // Get daily attendance rates for the period
        $period = Carbon::parse($dateFrom)->daysUntil($dateTo);

        foreach ($period as $date) {
            if ($date->isWeekday()) { // Assuming classes are on weekdays
                $schedules = TrainingSchedule::where('instructor_id', $instructorId)
                    ->whereDate('schedule_date', $date)
                    ->when($courseId, function($query) use ($courseId) {
                        $query->where('course_id', $courseId);
                    })
                    ->pluck('id');

                if ($schedules->count() > 0) {
                    $attendance = StudentAttendance::whereIn('schedule_id', $schedules)->get();
                    $total = $attendance->count();
                    $present = $attendance->whereIn('status', ['present', 'late'])->count();

                    $dates[] = $date->format('M d');
                    $rates[] = $total > 0 ? round(($present / $total) * 100) : 0;
                }
            }
        }

        return [
            'dates' => $dates,
            'rates' => $rates
        ];
    }
}
