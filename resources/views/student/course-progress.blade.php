@section('title', $course->objective . ' progress')
@php $pct = fn ($a, $b) => $b ? round($a / $b * 100) : 0; @endphp
<x-app-layout>
    <x-portal.page :title="$course->objective" subtitle="Your sessions, attendance and topics covered." :back="route('student.progress')">
        <div class="grid gap-4 sm:grid-cols-3">
            <x-portal.stat label="Sessions completed" :value="$stats['completed'] . ' / ' . $stats['total']" icon="calendar" :hint="$pct($stats['completed'], $stats['total']) . '% of scheduled sessions'" />
            <x-portal.stat label="Attendance" :value="$stats['recorded'] ? $pct($stats['attended'], $stats['recorded']) . '%' : '—'" icon="clipboard" :hint="$stats['attended'] . ' attended of ' . $stats['recorded'] . ' recorded'" :tone="$stats['recorded'] && $pct($stats['attended'], $stats['recorded']) < \App\Support\AdmissionSettings::attendanceThreshold() ? 'alert' : 'default'" />
            <x-portal.stat label="Topics covered" :value="$stats['topics_covered'] . ' / ' . $stats['topics_total']" icon="book" :hint="$instructor ? 'Instructor: ' . $instructor->firstName . ' ' . $instructor->lastName : 'Instructor being assigned'" />
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-3">
            <section class="portal-card xl:col-span-2">
                <div class="portal-card-header"><h2 class="portal-card-title">Sessions</h2></div>
                @if ($sessions->isEmpty())
                    <x-portal.empty icon="calendar" title="No sessions scheduled yet" text="Your timetable for this course will appear here." />
                @else
                    <div class="overflow-x-auto">
                        <table class="portal-table">
                            <thead><tr><th>Date</th><th>Topic</th><th>Type</th><th>Attendance</th></tr></thead>
                            <tbody>
                                @foreach ($sessions as $session)
                                    @php $record = $attendance->get($session->id); $past = $isPast($session); @endphp
                                    <tr>
                                        <td class="whitespace-nowrap">
                                            <span class="block font-semibold text-ink-950">{{ $session->schedule_date->format('D j M') }}</span>
                                            <span class="text-xs text-ink-400">{{ \Carbon\Carbon::parse($session->time_start)->format('g:ia') }}</span>
                                        </td>
                                        <td>{{ $session->topic?->topic ?? '—' }}</td>
                                        <td><span class="badge-grey">{{ ucfirst($session->session_type ?? 'theory') }}</span></td>
                                        <td>
                                            @if ($record)
                                                <span @class(['badge-green' => in_array($record->status, ['present', 'late']), 'badge-red' => $record->status === 'absent', 'badge-yellow' => $record->status === 'excused'])>{{ ucfirst($record->status) }}</span>
                                            @elseif ($past)
                                                <span class="text-xs text-ink-400">Not recorded</span>
                                            @else
                                                <span class="badge-dark">Upcoming</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <section class="portal-card">
                <div class="portal-card-header"><h2 class="portal-card-title">Curriculum</h2></div>
                <ol class="space-y-3 p-5">
                    @forelse ($course->curriculum as $n => $topic)
                        @php $covered = $coveredTopicIds->contains($topic->id); @endphp
                        <li class="flex gap-3">
                            <span @class(['mt-0.5 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-bold', 'bg-ink-950 text-brand-400' => $covered, 'bg-ink-100 text-ink-500' => ! $covered])>
                                @if ($covered)<x-site.icon name="check" class="h-3.5 w-3.5" />@else{{ $n + 1 }}@endif
                            </span>
                            <span @class(['text-sm', 'font-semibold text-ink-950' => $covered, 'text-ink-600' => ! $covered])>{{ $topic->topic }}</span>
                        </li>
                    @empty
                        <li class="text-sm text-ink-500">The curriculum for this course hasn't been published yet.</li>
                    @endforelse
                </ol>
            </section>
        </div>
    </x-portal.page>
</x-app-layout>
