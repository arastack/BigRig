<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EnrolmentBatch;
use App\Models\Role;
use App\Models\StudentAttendance;
use App\Models\StudentInstructorDistribution;
use App\Models\TrainingObjective;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Attendance per student and course across the school, with filters and CSV export.
 */
class AttendanceReportController extends Controller
{
    public const AT_RISK_BELOW = 75;

    public function index(Request $request)
    {
        $filters = $request->only(['batch', 'course', 'instructor', 'risk']);

        $assignments = StudentInstructorDistribution::with(['student', 'instructor', 'course', 'batch'])
            ->whereHas('student', fn ($q) => $q->whereNull('suspended_at'))
            ->when($filters['batch'] ?? null, fn ($q, $id) => $q->where('enrolment_batch_id', $id))
            ->when($filters['course'] ?? null, fn ($q, $id) => $q->where('course_id', $id))
            ->when($filters['instructor'] ?? null, fn ($q, $id) => $q->where('instructor_id', $id))
            ->get();

        // One query for all attendance counts, keyed by student and course
        $counts = StudentAttendance::whereIn('student_id', $assignments->pluck('student_id')->unique())
            ->selectRaw('student_id, course_id, status, COUNT(*) as total')
            ->groupBy('student_id', 'course_id', 'status')
            ->get()
            ->groupBy(fn ($row) => $row->student_id . '-' . $row->course_id);

        $rows = $assignments->map(function ($a) use ($counts) {
            $c = ($counts[$a->student_id . '-' . $a->course_id] ?? collect())->pluck('total', 'status');
            $recorded = (int) $c->sum();
            $attended = (int) (($c['present'] ?? 0) + ($c['late'] ?? 0));

            return [
                'student' => $a->student,
                'course' => $a->course,
                'instructor' => $a->instructor,
                'batch' => $a->batch,
                'present' => (int) ($c['present'] ?? 0),
                'late' => (int) ($c['late'] ?? 0),
                'absent' => (int) ($c['absent'] ?? 0),
                'excused' => (int) ($c['excused'] ?? 0),
                'recorded' => $recorded,
                'rate' => $recorded ? (int) round($attended / $recorded * 100) : null,
            ];
        })
            ->when(($filters['risk'] ?? null) === '1', fn ($rows) => $rows->filter(fn ($r) => $r['rate'] !== null && $r['rate'] < self::AT_RISK_BELOW))
            ->sortBy(fn ($r) => [$r['rate'] ?? 101, $r['student']?->lastName])
            ->values();

        if ($request->input('export') === 'csv') {
            return response()->streamDownload(function () use ($rows) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['Student', 'Email', 'Course', 'Instructor', 'Batch', 'Present', 'Late', 'Absent', 'Excused', 'Recorded', 'Attendance %']);
                foreach ($rows as $r) {
                    fputcsv($out, [$r['student']?->full_name, $r['student']?->email, $r['course']?->objective, trim($r['instructor']?->firstName . ' ' . $r['instructor']?->lastName), $r['batch']?->batch_name, $r['present'], $r['late'], $r['absent'], $r['excused'], $r['recorded'], $r['rate']]);
                }
                fclose($out);
            }, 'bigrig-attendance-' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv']);
        }

        $withRecords = $rows->whereNotNull('rate');

        return view('admin.attendance.index', [
            'rows' => $rows,
            'filters' => $filters,
            'stats' => [
                'average' => $withRecords->isNotEmpty() ? (int) round($withRecords->avg('rate')) : null,
                'atRisk' => $withRecords->filter(fn ($r) => $r['rate'] < self::AT_RISK_BELOW)->count(),
                'sessions' => StudentAttendance::count(),
                'students' => $rows->pluck('student.id')->unique()->count(),
            ],
            'batches' => EnrolmentBatch::orderByDesc('id')->get(),
            'courses' => TrainingObjective::orderBy('objective')->get(),
            'instructors' => User::whereHas('roles', fn ($q) => $q->where('portal', Role::PORTAL_INSTRUCTOR))->orderBy('firstName')->get(),
            'threshold' => self::AT_RISK_BELOW,
        ]);
    }
}
