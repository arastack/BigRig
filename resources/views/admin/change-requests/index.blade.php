@section('title', 'Schedule change requests')
@php
    $time = fn ($value) => $value ? \Illuminate\Support\Carbon::parse($value)->format('g:ia') : '—';
    $typeLabels = ['reschedule' => 'Reschedule', 'cancel' => 'Cancel session', 'substitute' => 'Substitute instructor'];
@endphp
<x-app-layout>
    <x-portal.page title="Change requests" subtitle="Instructors ask here to move, cancel or hand over a session. Approving a request updates the timetable straight away.">

        {{-- Status tabs --}}
        <nav class="mb-6 flex gap-1 overflow-x-auto rounded-xl bg-white p-1 shadow-sm ring-1 ring-ink-100" aria-label="Status">
            @foreach ($statuses as $key => $label)
                <a href="{{ route('admin.change-requests.index', ['status' => $key]) }}"
                   @class(['flex shrink-0 items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition', 'bg-ink-950 text-white' => $status === $key, 'text-ink-600 hover:bg-ink-50' => $status !== $key])
                   @if ($status === $key) aria-current="page" @endif>
                    {{ $label }}
                    <span @class(['rounded-full px-2 py-0.5 text-xs', 'bg-brand-400 text-ink-950' => $status === $key, 'bg-ink-100 text-ink-600' => $status !== $key])>{{ $counts[$key] ?? 0 }}</span>
                </a>
            @endforeach
        </nav>

        @if ($requests->isEmpty())
            <div class="portal-card">
                <x-portal.empty icon="calendar" :title="$status === 'pending' ? 'Nothing waiting for a decision' : 'No ' . strtolower($statuses[$status]) . ' requests yet'"
                    :text="$status === 'pending' ? 'When an instructor asks to change a session, it will appear here.' : null" />
            </div>
        @else
            <div class="space-y-4">
                @foreach ($requests as $item)
                    @php
                        $schedule = $item->schedule;
                        $requestedAt = $item->requested_at ?? $item->created_at;
                        $hasAttendance = $schedule && $attendanceMarked->has($schedule->id);
                    @endphp
                    <article class="portal-card" x-data="{ mode: null }">
                        {{-- Who and what --}}
                        <div class="portal-card-header">
                            <div class="flex min-w-0 items-center gap-3">
                                <x-portal.avatar :user="$item->instructor" size="h-9 w-9" />
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-ink-950">{{ $item->instructor ? $item->instructor->firstName . ' ' . $item->instructor->lastName : 'Former instructor' }}</p>
                                    <p class="text-xs text-ink-400" title="{{ $requestedAt }}">Asked {{ $requestedAt?->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span @class(['badge-red' => $item->type === 'cancel', 'badge-dark' => $item->type !== 'cancel'])>{{ $typeLabels[$item->type] ?? ucfirst($item->type) }}</span>
                                <x-portal.status :status="$item->status" />
                            </div>
                        </div>

                        <div class="grid gap-6 p-5 lg:grid-cols-3">
                            {{-- The session as it is --}}
                            <div>
                                <h3 class="portal-label">Session</h3>
                                @if ($schedule)
                                    <p class="font-semibold text-ink-950">{{ $schedule->course?->objective ?? 'Unknown course' }}</p>
                                    <p class="text-sm text-ink-600">{{ $schedule->topic?->topic ?? 'No topic' }} · {{ ucfirst($schedule->session_type ?? 'theory') }}</p>
                                    <p class="mt-2 flex items-center gap-2 text-sm text-ink-700"><x-site.icon name="calendar" class="h-4 w-4 text-ink-400" /> {{ $schedule->schedule_date?->format('D j M Y') }}</p>
                                    <p class="flex items-center gap-2 text-sm text-ink-700"><x-site.icon name="clock" class="h-4 w-4 text-ink-400" /> {{ $time($schedule->time_start) }}–{{ $time($schedule->time_stop) }}</p>
                                    <p class="flex items-center gap-2 text-sm text-ink-700"><x-site.icon name="user" class="h-4 w-4 text-ink-400" /> {{ $schedule->instructor ? $schedule->instructor->firstName . ' ' . $schedule->instructor->lastName : 'No instructor' }}</p>
                                    <p class="mt-1 text-xs text-ink-400">{{ $schedule->batch?->batch_name }}{{ $schedule->batch ? ' · ' : '' }}{{ count($schedule->studentIds()) }} {{ Str::plural('student', count($schedule->studentIds())) }}</p>
                                @else
                                    <p class="text-sm text-ink-500">{{ $item->type === 'cancel' && $item->status === 'approved' ? 'This session was cancelled and removed from the timetable.' : 'This session no longer exists.' }}</p>
                                @endif
                            </div>

                            {{-- What they asked for --}}
                            <div>
                                <h3 class="portal-label">Requested change</h3>
                                @if ($item->type === 'reschedule')
                                    <p class="text-sm text-ink-700">Move to</p>
                                    <p class="font-semibold text-ink-950">{{ $item->new_date?->format('D j M Y') ?? 'Date not given' }}</p>
                                    <p class="text-sm text-ink-700">{{ $item->new_time_start ? $time($item->new_time_start) . '–' . $time($item->new_time_stop) : 'Same time' }}</p>
                                @elseif ($item->type === 'substitute')
                                    <p class="text-sm text-ink-700">Hand over to</p>
                                    <p class="font-semibold text-ink-950">{{ $item->substituteInstructor ? $item->substituteInstructor->firstName . ' ' . $item->substituteInstructor->lastName : 'No one named — choose a substitute' }}</p>
                                @else
                                    <p class="font-semibold text-ink-950">Cancel this session</p>
                                    @if ($hasAttendance)
                                        <p class="mt-1 flex gap-1.5 text-xs font-medium text-red-700"><x-site.icon name="alert" class="h-4 w-4 shrink-0" /> Attendance has been taken, so this session can't be cancelled.</p>
                                    @endif
                                @endif
                            </div>

                            {{-- Why --}}
                            <div>
                                <h3 class="portal-label">Reason</h3>
                                <p class="text-sm text-ink-800">{!! nl2br(e($item->reason)) !!}</p>
                            </div>
                        </div>

                        @if ($item->status !== 'pending')
                            <div @class(['border-t px-5 py-3 text-sm', 'border-green-100 bg-green-50 text-green-900' => $item->status === 'approved', 'border-red-100 bg-red-50 text-red-900' => $item->status === 'rejected'])>
                                <p class="font-semibold">{{ ucfirst($item->status) }}{{ $item->reviewer ? ' by ' . $item->reviewer->firstName . ' ' . $item->reviewer->lastName : '' }}{{ $item->reviewed_at ? ' · ' . $item->reviewed_at->format('j M Y, g:ia') : '' }}</p>
                                @if ($item->admin_notes)<p class="mt-0.5">{!! nl2br(e($item->admin_notes)) !!}</p>@endif
                            </div>
                        @else
                            @can('manage_schedule_change_requests')
                                <div class="border-t border-ink-100 px-5 py-4">
                                    <div class="flex flex-wrap gap-2" x-show="!mode">
                                        @if ($schedule && ! ($item->type === 'cancel' && $hasAttendance))
                                            <button type="button" class="btn-dark btn-sm" @click="mode = 'approve'"><x-site.icon name="check" class="h-4 w-4" /> Approve…</button>
                                        @endif
                                        <button type="button" class="btn-ghost btn-sm text-red-600" @click="mode = 'reject'">Reject…</button>
                                    </div>

                                    {{-- Approve --}}
                                    @if ($schedule)
                                        <form method="POST" action="{{ route('admin.change-requests.update', $item) }}" class="space-y-4" x-show="mode === 'approve'" x-cloak
                                              @if ($item->type === 'cancel') onsubmit="return confirm('Cancel this session? It will be removed from the timetable for the instructor and students.')" @endif>
                                            @csrf @method('PUT')
                                            <input type="hidden" name="decision" value="approve">
                                            @if ($item->type === 'reschedule')
                                                <p class="text-sm text-ink-600">Check the new slot. You can adjust it before approving.</p>
                                                <div class="grid gap-3 sm:grid-cols-3">
                                                    <div>
                                                        <label class="portal-label" for="new_date_{{ $item->id }}">New date</label>
                                                        <input id="new_date_{{ $item->id }}" type="date" name="new_date" required min="{{ now()->format('Y-m-d') }}" value="{{ ($item->new_date ?? $schedule->schedule_date)?->format('Y-m-d') }}" class="portal-input">
                                                    </div>
                                                    <div>
                                                        <label class="portal-label" for="new_time_start_{{ $item->id }}">Starts</label>
                                                        <input id="new_time_start_{{ $item->id }}" type="time" name="new_time_start" required value="{{ substr($item->new_time_start ?? $schedule->time_start, 0, 5) }}" class="portal-input">
                                                    </div>
                                                    <div>
                                                        <label class="portal-label" for="new_time_stop_{{ $item->id }}">Finishes</label>
                                                        <input id="new_time_stop_{{ $item->id }}" type="time" name="new_time_stop" required value="{{ substr($item->new_time_stop ?? $schedule->time_stop, 0, 5) }}" class="portal-input">
                                                    </div>
                                                </div>
                                            @elseif ($item->type === 'substitute')
                                                <div class="max-w-md">
                                                    <label class="portal-label" for="substitute_{{ $item->id }}">Substitute instructor</label>
                                                    <select id="substitute_{{ $item->id }}" name="substitute_instructor_id" required class="portal-input">
                                                        <option value="">Choose an instructor</option>
                                                        @foreach ($instructors->where('id', '!=', $schedule->instructor_id) as $instructor)
                                                            <option value="{{ $instructor->id }}" @selected($item->substitute_instructor_id === $instructor->id)>{{ $instructor->firstName }} {{ $instructor->lastName }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @else
                                                <p class="text-sm text-ink-600">The session will be deleted from the timetable. Students and the instructor will no longer see it.</p>
                                            @endif
                                            <div>
                                                <label class="portal-label" for="approve_notes_{{ $item->id }}">Note to the instructor <span class="normal-case tracking-normal text-ink-400">(optional)</span></label>
                                                <textarea id="approve_notes_{{ $item->id }}" name="admin_notes" rows="2" maxlength="1000" class="portal-input"></textarea>
                                            </div>
                                            <div class="flex flex-wrap gap-2">
                                                <button class="btn-brand btn-sm">{{ $item->type === 'cancel' ? 'Approve and cancel session' : 'Approve and update timetable' }}</button>
                                                <button type="button" class="btn-ghost btn-sm" @click="mode = null">Back</button>
                                            </div>
                                        </form>
                                    @endif

                                    {{-- Reject --}}
                                    <form method="POST" action="{{ route('admin.change-requests.update', $item) }}" class="space-y-3" x-show="mode === 'reject'" x-cloak>
                                        @csrf @method('PUT')
                                        <input type="hidden" name="decision" value="reject">
                                        <div>
                                            <label class="portal-label" for="reject_notes_{{ $item->id }}">Why are you rejecting this?</label>
                                            <textarea id="reject_notes_{{ $item->id }}" name="admin_notes" rows="2" required maxlength="1000" placeholder="The instructor will see this note" class="portal-input"></textarea>
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            <button class="btn-danger btn-sm">Reject request</button>
                                            <button type="button" class="btn-ghost btn-sm" @click="mode = null">Back</button>
                                        </div>
                                    </form>
                                </div>
                            @endcan
                        @endif
                    </article>
                @endforeach
            </div>

            @if ($requests->hasPages())
                <div class="mt-6">{{ $requests->links() }}</div>
            @endif
        @endif
    </x-portal.page>
</x-app-layout>
