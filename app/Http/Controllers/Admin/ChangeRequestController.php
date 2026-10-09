<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\ScheduleChangeRequest;
use App\Models\StudentAttendance;
use App\Models\TrainingSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Instructors ask to reschedule, cancel or hand over a session; admins approve or reject.
 */
class ChangeRequestController extends Controller
{
    public const STATUSES = [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ];

    public function index(Request $request)
    {
        $status = array_key_exists($request->input('status'), self::STATUSES) ? $request->input('status') : 'pending';

        $requests = ScheduleChangeRequest::with([
            'schedule.course', 'schedule.topic', 'schedule.instructor', 'schedule.batch',
            'instructor', 'substituteInstructor', 'reviewer',
        ])
            ->where('status', $status)
            ->orderBy($status === 'pending' ? 'requested_at' : 'reviewed_at', $status === 'pending' ? 'asc' : 'desc')
            ->paginate(20)
            ->withQueryString();

        $counts = ScheduleChangeRequest::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        $scheduleIds = $requests->pluck('schedule_id')->filter()->unique();

        return view('admin.change-requests.index', [
            'requests' => $requests,
            'status' => $status,
            'statuses' => self::STATUSES,
            'counts' => $counts,
            'instructors' => $status === 'pending' ? $this->instructors() : collect(),
            'attendanceMarked' => StudentAttendance::whereIn('schedule_id', $scheduleIds)->distinct()->pluck('schedule_id')->flip(),
        ]);
    }

    public function update(Request $request, ScheduleChangeRequest $changeRequest)
    {
        if ($changeRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been ' . $changeRequest->status . '.');
        }

        $data = $request->validate([
            'decision' => ['required', Rule::in(['approve', 'reject'])],
            'admin_notes' => ['nullable', 'required_if:decision,reject', 'string', 'max:1000'],
        ], ['admin_notes.required_if' => 'Tell the instructor why the request was rejected.']);

        if ($data['decision'] === 'reject') {
            $this->review($changeRequest, 'rejected', $data['admin_notes'], $request->user());

            return back()->with('success', 'Request rejected. The session stays as it was.');
        }

        $schedule = $changeRequest->schedule;

        if (! $schedule) {
            return back()->with('error', 'The session for this request no longer exists. Reject the request instead.');
        }

        return match ($changeRequest->type) {
            'reschedule' => $this->approveReschedule($request, $changeRequest, $schedule, $data['admin_notes'] ?? null),
            'substitute' => $this->approveSubstitute($request, $changeRequest, $schedule, $data['admin_notes'] ?? null),
            'cancel' => $this->approveCancel($request, $changeRequest, $schedule, $data['admin_notes'] ?? null),
        };
    }

    private function approveReschedule(Request $request, ScheduleChangeRequest $changeRequest, TrainingSchedule $schedule, ?string $notes)
    {
        // The admin may adjust the proposed slot; anything left out keeps the current value
        $slot = $request->validate([
            'new_date' => ['required', 'date', 'after_or_equal:today'],
            'new_time_start' => ['required', 'date_format:H:i'],
            'new_time_stop' => ['required', 'date_format:H:i', 'after:new_time_start'],
        ], [], ['new_date' => 'new date', 'new_time_start' => 'start time', 'new_time_stop' => 'finish time']);

        if ($clash = $this->clash($schedule->instructor_id, $slot['new_date'], $slot['new_time_start'], $slot['new_time_stop'], $schedule->id)) {
            return back()->withInput()->with('error', $clash);
        }

        DB::transaction(function () use ($changeRequest, $schedule, $slot, $notes, $request) {
            $schedule->update([
                'schedule_date' => $slot['new_date'],
                'time_start' => $slot['new_time_start'],
                'time_stop' => $slot['new_time_stop'],
            ]);

            $changeRequest->fill([
                'new_date' => $slot['new_date'],
                'new_time_start' => $slot['new_time_start'],
                'new_time_stop' => $slot['new_time_stop'],
            ]);
            $this->review($changeRequest, 'approved', $notes, $request->user());
        });

        return back()->with('success', 'Approved. The session now runs on ' . Carbon::parse($slot['new_date'])->format('D j M Y') . ', ' . $this->time($slot['new_time_start']) . '–' . $this->time($slot['new_time_stop']) . '.');
    }

    private function approveSubstitute(Request $request, ScheduleChangeRequest $changeRequest, TrainingSchedule $schedule, ?string $notes)
    {
        $data = $request->validate([
            'substitute_instructor_id' => ['required', 'integer', Rule::notIn([$schedule->instructor_id])],
        ], [
            'substitute_instructor_id.required' => 'Choose the instructor who will take this session.',
            'substitute_instructor_id.not_in' => 'Choose a different instructor from the one already teaching this session.',
        ]);

        $substitute = $this->instructors()->firstWhere('id', (int) $data['substitute_instructor_id']);

        if (! $substitute) {
            return back()->withInput()->with('error', 'Choose an active instructor account.');
        }

        if ($clash = $this->clash($substitute->id, $schedule->schedule_date->format('Y-m-d'), $schedule->time_start, $schedule->time_stop, $schedule->id, $substitute)) {
            return back()->withInput()->with('error', $clash);
        }

        DB::transaction(function () use ($changeRequest, $schedule, $substitute, $notes, $request) {
            $schedule->update(['instructor_id' => $substitute->id]);
            $changeRequest->substitute_instructor_id = $substitute->id;
            $this->review($changeRequest, 'approved', $notes, $request->user());
        });

        return back()->with('success', "Approved. {$substitute->firstName} {$substitute->lastName} now teaches this session.");
    }

    private function approveCancel(Request $request, ScheduleChangeRequest $changeRequest, TrainingSchedule $schedule, ?string $notes)
    {
        if (StudentAttendance::where('schedule_id', $schedule->id)->exists()) {
            return back()->with('error', 'Attendance has already been taken for this session, so it can\'t be cancelled. Reject the request, or ask the instructor to reschedule instead.');
        }

        DB::transaction(function () use ($changeRequest, $schedule, $notes, $request) {
            $this->review($changeRequest, 'approved', $notes, $request->user());

            // Other open requests for the same session no longer apply
            ScheduleChangeRequest::where('schedule_id', $schedule->id)
                ->whereKeyNot($changeRequest->id)
                ->where('status', 'pending')
                ->get()
                ->each(fn ($other) => $this->review($other, 'rejected', 'The session was cancelled.', $request->user()));

            $schedule->delete();
        });

        return back()->with('success', 'Approved. The session has been cancelled and removed from the timetable.');
    }

    private function review(ScheduleChangeRequest $changeRequest, string $status, ?string $notes, User $reviewer): void
    {
        $changeRequest->forceFill([
            'status' => $status,
            'admin_notes' => $notes,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            // requested_at auto-updates on any change in the database; keep the original time
            'requested_at' => $changeRequest->requested_at ?? $changeRequest->created_at,
        ])->save();
    }

    /**
     * A message when the instructor already teaches another session that overlaps the slot.
     */
    private function clash(int $instructorId, string $date, string $start, string $stop, int $ignoreId, ?User $who = null): ?string
    {
        $other = TrainingSchedule::with('course')
            ->where('instructor_id', $instructorId)
            ->whereDate('schedule_date', $date)
            ->whereKeyNot($ignoreId)
            ->where('time_start', '<', $stop)
            ->where('time_stop', '>', $start)
            ->first();

        if (! $other) {
            return null;
        }

        $name = $who ? "{$who->firstName} {$who->lastName} is" : 'The instructor is';

        return "{$name} already teaching " . ($other->course?->objective ?? 'another session') . ' from ' . $this->time($other->time_start) . ' to ' . $this->time($other->time_stop) . ' that day. Pick a different time or instructor.';
    }

    private function instructors()
    {
        return User::query()
            ->whereNull('suspended_at')
            ->where('user_active', true)
            ->whereHas('roles', fn ($q) => $q->where('portal', Role::PORTAL_INSTRUCTOR))
            ->orderBy('firstName')
            ->get();
    }

    private function time(?string $value): string
    {
        return $value ? Carbon::parse($value)->format('g:ia') : '—';
    }
}
