@section('title', 'Attendance')
<x-app-layout>
    <x-portal.page title="Attendance" subtitle="How often each student attends their training, by course. Students below {{ $threshold }}% are flagged so instructors can follow up.">
        <x-slot:actions>
            <a href="{{ route('admin.attendance', array_merge(request()->query(), ['export' => 'csv'])) }}" class="btn-ghost btn-sm"><x-site.icon name="download" class="h-4 w-4" /> Export CSV</a>
        </x-slot:actions>

        <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-portal.stat label="Average attendance" :value="$stats['average'] !== null ? $stats['average'] . '%' : '—'" icon="clipboard" />
            <x-portal.stat label="Students below {{ $threshold }}%" :value="$stats['atRisk']" icon="alert" :tone="$stats['atRisk'] ? 'alert' : 'default'" :href="route('admin.attendance', array_merge($filters, ['risk' => 1]))" />
            <x-portal.stat label="Students in this view" :value="$stats['students']" icon="graduation" />
            <x-portal.stat label="Attendance records" :value="number_format($stats['sessions'])" icon="calendar" hint="All time, whole school" />
        </div>

        <div class="portal-card">
            <form method="GET" class="grid gap-3 border-b border-ink-100 p-4 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_1fr_auto_auto]">
                <select name="batch" class="portal-input" aria-label="Batch">
                    <option value="">All batches</option>
                    @foreach ($batches as $batch)<option value="{{ $batch->id }}" @selected(($filters['batch'] ?? null) == $batch->id)>{{ $batch->batch_name }}</option>@endforeach
                </select>
                <select name="course" class="portal-input" aria-label="Course">
                    <option value="">All courses</option>
                    @foreach ($courses as $course)<option value="{{ $course->id }}" @selected(($filters['course'] ?? null) == $course->id)>{{ $course->objective }}</option>@endforeach
                </select>
                <select name="instructor" class="portal-input" aria-label="Instructor">
                    <option value="">All instructors</option>
                    @foreach ($instructors as $instructor)<option value="{{ $instructor->id }}" @selected(($filters['instructor'] ?? null) == $instructor->id)>{{ $instructor->firstName }} {{ $instructor->lastName }}</option>@endforeach
                </select>
                <label class="flex items-center gap-2 text-sm font-medium text-ink-700"><input type="checkbox" name="risk" value="1" @checked(($filters['risk'] ?? null) === '1') class="rounded border-ink-300 text-ink-950 focus:ring-brand-400"> Below {{ $threshold }}% only</label>
                <button class="btn-dark btn-sm"><x-site.icon name="filter" class="h-4 w-4" /> Filter</button>
            </form>

            @if ($rows->isEmpty())
                <x-portal.empty icon="clipboard" title="No students to show" text="Students appear here once they're enrolled and assigned an instructor." />
            @else
                <div class="overflow-x-auto">
                    <table class="portal-table">
                        <thead>
                            <tr><th>Student</th><th>Course</th><th>Instructor</th><th class="text-center">Present</th><th class="text-center">Late</th><th class="text-center">Absent</th><th class="text-center">Excused</th><th class="text-right">Attendance</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $row)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.admissions.show', $row['student']) }}" class="flex items-center gap-3 font-semibold text-ink-950 hover:underline">
                                            <x-portal.avatar :user="$row['student']" size="h-8 w-8" /> {{ $row['student']->firstName }} {{ $row['student']->lastName }}
                                        </a>
                                    </td>
                                    <td>{{ $row['course']?->objective }}</td>
                                    <td>{{ $row['instructor']?->firstName }} {{ $row['instructor']?->lastName }}</td>
                                    <td class="text-center">{{ $row['present'] }}</td>
                                    <td class="text-center">{{ $row['late'] }}</td>
                                    <td class="text-center">{{ $row['absent'] }}</td>
                                    <td class="text-center">{{ $row['excused'] }}</td>
                                    <td class="text-right">
                                        @if ($row['rate'] === null)
                                            <span class="text-xs text-ink-400">No sessions yet</span>
                                        @else
                                            <span @class(['badge-red' => $row['rate'] < $threshold, 'badge-green' => $row['rate'] >= $threshold])>{{ $row['rate'] }}%</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </x-portal.page>
</x-app-layout>
