@section('title', 'Training schedule')
@php
    $user = auth()->user();
    $views = ['upcoming' => 'Upcoming', 'today' => 'Today', 'week' => 'Next 7 days', 'past' => 'Past', 'all' => 'All'];
    $byDay = $sessions->getCollection()->groupBy(fn ($s) => $s->schedule_date->format('Y-m-d'));
@endphp
<x-app-layout>
    <x-portal.page title="Training schedule" subtitle="Every theory and practical session, by day. Instructors see their own sessions in the instructor portal.">
        <x-slot:actions>
            @can('create_training_schedule')
                <a href="{{ route('schedule.new') }}" class="btn-brand btn-sm"><x-site.icon name="plus" class="h-4 w-4" /> Schedule sessions</a>
            @endcan
        </x-slot:actions>

        <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-portal.stat label="Sessions today" :value="$stats['today']" icon="clock" :href="route('schedule', ['when' => 'today'])" />
            <x-portal.stat label="Next 7 days" :value="$stats['week']" icon="calendar" :href="route('schedule', ['when' => 'week'])" />
            <x-portal.stat label="All upcoming" :value="$stats['upcoming']" icon="trending-up" />
            @can('manage_schedule_change_requests')
                <x-portal.stat label="Change requests waiting" :value="$stats['pendingChanges']" icon="refresh" :tone="$stats['pendingChanges'] ? 'alert' : 'default'" :href="route('admin.change-requests.index')" />
            @endcan
        </div>

        <nav class="mb-4 flex gap-1 overflow-x-auto rounded-xl bg-white p-1 shadow-sm ring-1 ring-ink-100" aria-label="Period">
            @foreach ($views as $key => $label)
                <a href="{{ route('schedule', array_merge($filters, ['when' => $key])) }}" @class(['shrink-0 rounded-lg px-4 py-2 text-sm font-semibold transition', 'bg-ink-950 text-white' => $when === $key, 'text-ink-600 hover:bg-ink-50' => $when !== $key]) @if ($when === $key) aria-current="page" @endif>{{ $label }}</a>
            @endforeach
        </nav>

        <div class="portal-card">
            <form method="GET" class="grid gap-3 border-b border-ink-100 p-4 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_1fr_auto]">
                <input type="hidden" name="when" value="{{ $when }}">
                <select name="course" class="portal-input" aria-label="Course">
                    <option value="">All courses</option>
                    @foreach ($courses as $course)<option value="{{ $course->id }}" @selected(($filters['course'] ?? null) == $course->id)>{{ $course->objective }}</option>@endforeach
                </select>
                <select name="batch" class="portal-input" aria-label="Batch">
                    <option value="">All batches</option>
                    @foreach ($batches as $batch)<option value="{{ $batch->id }}" @selected(($filters['batch'] ?? null) == $batch->id)>{{ $batch->batch_name }}</option>@endforeach
                </select>
                <select name="instructor" class="portal-input" aria-label="Instructor">
                    <option value="">All instructors</option>
                    @foreach ($instructors as $instructor)<option value="{{ $instructor->id }}" @selected(($filters['instructor'] ?? null) == $instructor->id)>{{ $instructor->firstName }} {{ $instructor->lastName }}</option>@endforeach
                </select>
                <div class="flex gap-2">
                    <button class="btn-dark btn-sm flex-1"><x-site.icon name="filter" class="h-4 w-4" /> Filter</button>
                    @if (array_filter(\Illuminate\Support\Arr::except($filters, 'when')))
                        <a href="{{ route('schedule', ['when' => $when]) }}" class="btn-ghost btn-sm">Clear</a>
                    @endif
                </div>
            </form>

            @if ($sessions->isEmpty())
                <x-portal.empty icon="calendar" title="No sessions to show" text="Nothing matches this period and these filters.">
                    @can('create_training_schedule')<a href="{{ route('schedule.new') }}" class="btn-dark btn-sm mt-4">Schedule sessions</a>@endcan
                </x-portal.empty>
            @else
                @foreach ($byDay as $day => $daySessions)
                    @php $date = \Carbon\Carbon::parse($day); @endphp
                    <div class="border-b border-ink-100 last:border-b-0">
                        <h2 class="sticky top-16 z-10 flex items-baseline gap-3 bg-ink-50/95 px-5 py-2 backdrop-blur">
                            <span class="font-display text-lg font-bold uppercase text-ink-950">{{ $date->isToday() ? 'Today' : ($date->isTomorrow() ? 'Tomorrow' : $date->format('l')) }}</span>
                            <span class="text-sm text-ink-500">{{ $date->format('j F Y') }}</span>
                            <span class="ml-auto text-xs text-ink-400">{{ $daySessions->count() }} {{ \Illuminate\Support\Str::plural('session', $daySessions->count()) }}</span>
                        </h2>
                        <ul class="divide-y divide-ink-100">
                            @foreach ($daySessions as $session)
                                @php $studentCount = count($session->studentIds()); $taken = $attendanceTaken->has($session->id); @endphp
                                <li class="flex flex-col gap-3 px-5 py-4 md:flex-row md:items-center">
                                    <div class="w-32 shrink-0">
                                        <p class="font-display text-xl font-bold text-ink-950">{{ \Carbon\Carbon::parse($session->time_start)->format('g:ia') }}</p>
                                        <p class="text-xs text-ink-400">until {{ \Carbon\Carbon::parse($session->time_stop)->format('g:ia') }}</p>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-semibold text-ink-950">{{ $session->topic?->topic ?? 'Training session' }}</p>
                                        <p class="text-sm text-ink-500">
                                            {{ $session->course?->objective }}
                                            @if ($session->batch) · {{ $session->batch->batch_name }}@endif
                                        </p>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2 md:w-[26rem] md:justify-end">
                                        <span class="flex items-center gap-2 text-sm text-ink-700 md:w-40">
                                            <x-portal.avatar :user="$session->instructor" size="h-7 w-7" />
                                            <span class="truncate">{{ $session->instructor?->firstName }} {{ $session->instructor?->lastName }}</span>
                                        </span>
                                        <span @class(['badge-dark' => $session->session_type === 'practical', 'badge-grey' => $session->session_type !== 'practical'])>{{ ucfirst($session->session_type ?? 'theory') }}</span>
                                        <span class="badge-grey" title="Students in this session">{{ $studentCount }} {{ \Illuminate\Support\Str::plural('student', $studentCount) }}</span>
                                        @if ($taken)<span class="badge-green">Attendance taken</span>@endif
                                        @can('update_training_schedule')
                                            <a href="{{ route('schedule.edit', $session->id) }}" class="btn-ghost btn-xs" aria-label="Edit session"><x-site.icon name="pencil" class="h-4 w-4" /></a>
                                        @endcan
                                        @can('delete_training_schedule')
                                            @unless ($taken)
                                                <form method="POST" action="{{ route('schedule.destroy', $session->id) }}" onsubmit="return confirm('Delete this session? Students will no longer see it in their timetable.')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn-ghost btn-xs text-red-600" aria-label="Delete session"><x-site.icon name="trash" class="h-4 w-4" /></button>
                                                </form>
                                            @endunless
                                        @endcan
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
                @if ($sessions->hasPages())
                    <div class="border-t border-ink-100 px-4 py-3">{{ $sessions->links() }}</div>
                @endif
            @endif
        </div>
    </x-portal.page>
</x-app-layout>
