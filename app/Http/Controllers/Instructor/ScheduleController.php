<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TrainingSchedule;
use App\Models\StudentInstructorDistribution;
use App\Models\TrainingObjective;
use App\Models\ScheduleChangeRequest;
use App\Models\Curriculum;
use Carbon\Carbon;
use DB;

class ScheduleController extends Controller
{
    /**
     * Display instructor's schedule
     */
    public function index(Request $request)
    {
        $instructor = Auth::user();
        $view = $request->get('view', 'week'); // week, month, list
        $date = $request->get('date', Carbon::now()->format('Y-m-d'));
        $currentDate = Carbon::parse($date);

        // Get base query
        $schedulesQuery = TrainingSchedule::where('instructor_id', $instructor->id)
            ->with(['course', 'topic', 'batch']);

        // Apply view filters
        switch($view) {
            case 'week':
                $startDate = $currentDate->copy()->startOfWeek();
                $endDate = $currentDate->copy()->endOfWeek();
                $schedules = $schedulesQuery->whereBetween('schedule_date', [$startDate, $endDate])
                    ->orderBy('schedule_date')
                    ->orderBy('time_start')
                    ->get();
                break;

            case 'month':
                $startDate = $currentDate->copy()->startOfMonth();
                $endDate = $currentDate->copy()->endOfMonth();
                $schedules = $schedulesQuery->whereBetween('schedule_date', [$startDate, $endDate])
                    ->orderBy('schedule_date')
                    ->orderBy('time_start')
                    ->get();
                break;

            case 'list':
            default:
                $schedules = $schedulesQuery->where('schedule_date', '>=', Carbon::today())
                    ->orderBy('schedule_date')
                    ->orderBy('time_start')
                    ->paginate(20);
                break;
        }

        // Get today's classes
        $todaySchedules = TrainingSchedule::where('instructor_id', $instructor->id)
            ->whereDate('schedule_date', Carbon::today())
            ->with(['course', 'topic', 'batch'])
            ->orderBy('time_start')
            ->get();

        // Get upcoming classes (next 7 days)
        $upcomingSchedules = TrainingSchedule::where('instructor_id', $instructor->id)
            ->whereBetween('schedule_date', [Carbon::tomorrow(), Carbon::now()->addDays(7)])
            ->with(['course', 'topic', 'batch'])
            ->orderBy('schedule_date')
            ->orderBy('time_start')
            ->get();

        // Get schedule statistics
        $stats = [
            'total_classes_week' => TrainingSchedule::where('instructor_id', $instructor->id)
                ->whereBetween('schedule_date', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ])
                ->count(),
            'total_classes_month' => TrainingSchedule::where('instructor_id', $instructor->id)
                ->whereMonth('schedule_date', Carbon::now()->month)
                ->whereYear('schedule_date', Carbon::now()->year)
                ->count(),
            'classes_today' => $todaySchedules->count(),
            'pending_requests' => ScheduleChangeRequest::where('instructor_id', $instructor->id)
                ->where('status', 'pending')
                ->count(),
        ];

        // Get courses for filters
        $courses = TrainingObjective::whereIn('id',
            StudentInstructorDistribution::where('instructor_id', $instructor->id)
                ->pluck('course_id')
                ->unique()
        )->get();

        // Prepare calendar data for month view
        $calendarData = [];
        if ($view == 'month') {
            $calendarData = $this->prepareCalendarData($schedules, $startDate, $endDate);
        }

        return view('instructor.schedule.index', compact(
            'schedules',
            'todaySchedules',
            'upcomingSchedules',
            'stats',
            'courses',
            'view',
            'currentDate',
            'calendarData'
        ));
    }

    /**
     * Show schedule details
     */
    public function show($id)
    {
        $instructor = Auth::user();
        $schedule = TrainingSchedule::where('instructor_id', $instructor->id)
            ->with(['course', 'topic', 'batch'])
            ->findOrFail($id);

        // Get students for this class
        $students = StudentInstructorDistribution::where('instructor_id', $instructor->id)
            ->where('course_id', $schedule->course_id)
            ->with('student')
            ->get()
            ->pluck('student');

        // Get attendance if class has passed
        $attendance = [];
        if (Carbon::parse($schedule->schedule_date)->isPast()) {
            $attendance = \App\Models\StudentAttendance::where('schedule_id', $schedule->id)
                ->with('student')
                ->get();
        }

        return view('instructor.schedule.show', compact('schedule', 'students', 'attendance'));
    }

    /**
     * Request schedule change
     */
    public function requestChange(Request $request, $id)
    {
        $instructor = Auth::user();
        $schedule = TrainingSchedule::where('instructor_id', $instructor->id)->findOrFail($id);

        if ($request->isMethod('post')) {
            $request->validate([
                'reason' => 'required|string|max:500',
                'new_date' => 'nullable|date|after:today',
                'new_time_start' => 'nullable|date_format:H:i',
                'new_time_stop' => 'nullable|date_format:H:i|after:new_time_start',
                'type' => 'required|in:reschedule,cancel,substitute'
            ]);

            // Create change request
            ScheduleChangeRequest::create([
                'schedule_id' => $schedule->id,
                'instructor_id' => $instructor->id,
                'type' => $request->type,
                'reason' => $request->reason,
                'new_date' => $request->new_date,
                'new_time_start' => $request->new_time_start,
                'new_time_stop' => $request->new_time_stop,
                'status' => 'pending',
                'requested_at' => now(),
            ]);

            return redirect()->route('instructor.schedule')
                ->with('success', 'Schedule change request submitted successfully. You will be notified once it\'s reviewed.');
        }

        return view('instructor.schedule.request-change', compact('schedule'));
    }

    /**
     * View change requests
     */
    public function changeRequests()
    {
        $instructor = Auth::user();

        $requests = ScheduleChangeRequest::where('instructor_id', $instructor->id)
            ->with(['schedule.course', 'schedule.topic'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('instructor.schedule.change-requests', compact('requests'));
    }

    /**
     * Calendar view
     */
    public function calendar()
    {
        $instructor = Auth::user();

        // Get all schedules for calendar
        $schedules = TrainingSchedule::where('instructor_id', $instructor->id)
            ->with(['course', 'topic', 'batch'])
            ->get()
            ->map(function($schedule) {
                return [
                    'id' => $schedule->id,
                    'title' => $schedule->course->objective . ' - ' . $schedule->topic->topic,
                    'start' => $schedule->schedule_date . 'T' . $schedule->time_start,
                    'end' => $schedule->schedule_date . 'T' . $schedule->time_stop,
                    'color' => $schedule->session_type == 'theory' ? '#8B5CF6' : '#10B981',
                    'url' => route('instructor.schedule.show', $schedule->id),
                    'extendedProps' => [
                        'course' => $schedule->course->objective,
                        'topic' => $schedule->topic->topic,
                        'batch' => $schedule->batch->batch_name,
                        'type' => $schedule->session_type,
                    ]
                ];
            });

        return view('instructor.schedule.calendar', compact('schedules'));
    }

    /**
     * Export schedule
     */
    public function export(Request $request)
    {
        $instructor = Auth::user();
        $format = $request->get('format', 'pdf'); // pdf, excel, ics
        $range = $request->get('range', 'month'); // week, month, all

        // Get schedules based on range
        $schedulesQuery = TrainingSchedule::where('instructor_id', $instructor->id)
            ->with(['course', 'topic', 'batch']);

        switch($range) {
            case 'week':
                $schedulesQuery->whereBetween('schedule_date', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ]);
                break;
            case 'month':
                $schedulesQuery->whereMonth('schedule_date', Carbon::now()->month)
                    ->whereYear('schedule_date', Carbon::now()->year);
                break;
            // 'all' doesn't need additional filters
        }

        $schedules = $schedulesQuery->orderBy('schedule_date')->orderBy('time_start')->get();

        // Handle different export formats
        switch($format) {
            case 'pdf':
                return $this->exportToPDF($schedules);
            case 'excel':
                return $this->exportToExcel($schedules);
            case 'ics':
                return $this->exportToICS($schedules);
            default:
                return redirect()->back()->with('error', 'Invalid export format');
        }
    }

    /**
     * Prepare calendar data for month view
     */
    private function prepareCalendarData($schedules, $startDate, $endDate)
    {
        $calendarData = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $daySchedules = $schedules->filter(function($schedule) use ($currentDate) {
                return Carbon::parse($schedule->schedule_date)->isSameDay($currentDate);
            });

            $calendarData[$currentDate->format('Y-m-d')] = $daySchedules;
            $currentDate->addDay();
        }

        return $calendarData;
    }

    /**
     * Export to PDF (placeholder)
     */
    private function exportToPDF($schedules)
    {
        $instructor = Auth::user();

        return \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.instructor-schedule', compact('schedules', 'instructor'))
            ->setPaper('a4', 'landscape')
            ->download('bigrig-schedule-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export to CSV (opens in Excel)
     */
    private function exportToExcel($schedules)
    {
        return response()->streamDownload(function () use ($schedules) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Date', 'Start', 'End', 'Course', 'Topic', 'Type', 'Batch', 'Students']);
            foreach ($schedules as $s) {
                fputcsv($out, [
                    $s->schedule_date->format('Y-m-d'),
                    Carbon::parse($s->time_start)->format('H:i'),
                    Carbon::parse($s->time_stop)->format('H:i'),
                    $s->course?->objective, $s->topic?->topic, ucfirst($s->session_type ?? 'theory'),
                    $s->batch?->batch_name, count($s->studentIds()),
                ]);
            }
            fclose($out);
        }, 'bigrig-schedule-' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv']);
    }

    /**
     * Export to ICS (iCalendar)
     */
    private function exportToICS($schedules)
    {
        $icsContent = "BEGIN:VCALENDAR\r\n";
        $icsContent .= "VERSION:2.0\r\n";
        $icsContent .= "PRODID:-//BigRig Training//Instructor Schedule//EN\r\n";
        $icsContent .= "CALSCALE:GREGORIAN\r\n";
        $icsContent .= "METHOD:PUBLISH\r\n";

        foreach ($schedules as $schedule) {
            $startDateTime = Carbon::parse($schedule->schedule_date . ' ' . $schedule->time_start);
            $endDateTime = Carbon::parse($schedule->schedule_date . ' ' . $schedule->time_stop);

            $icsContent .= "BEGIN:VEVENT\r\n";
            $icsContent .= "UID:" . md5($schedule->id . '@bigrig.com') . "\r\n";
            $icsContent .= "DTSTART:" . $startDateTime->format('Ymd\THis') . "\r\n";
            $icsContent .= "DTEND:" . $endDateTime->format('Ymd\THis') . "\r\n";
            $icsContent .= "SUMMARY:" . $schedule->course->objective . " - " . $schedule->topic->topic . "\r\n";
            $icsContent .= "DESCRIPTION:Course: " . $schedule->course->objective . "\\nTopic: " . $schedule->topic->topic . "\\nBatch: " . $schedule->batch->batch_name . "\\nType: " . ucfirst($schedule->session_type) . "\r\n";
            $icsContent .= "LOCATION:BigRig Training Center\r\n";
            $icsContent .= "END:VEVENT\r\n";
        }

        $icsContent .= "END:VCALENDAR\r\n";

        return response($icsContent)
            ->header('Content-Type', 'text/calendar')
            ->header('Content-Disposition', 'attachment; filename="instructor-schedule.ics"');
    }
}
