@section('title', 'Dashboard')
@php
    $user = auth()->user();
    $next = $upcomingSchedule->first();
@endphp
<x-app-layout>
    <x-portal.page :title="'Welcome, ' . $user->firstName" :subtitle="($user->enrolmentBatch?->batch_name ? $user->enrolmentBatch->batch_name . ' · ' : '') . $progress->label()">

        {{-- Next session hero --}}
        <section class="on-dark relative mb-6 overflow-hidden rounded-xl bg-ink-950 p-6 text-white sm:p-8">
            <div class="pointer-events-none absolute -right-16 top-0 hidden h-full w-1/3 skew-x-[-12deg] bg-brand-400 md:block" aria-hidden="true"></div>
            <div class="relative z-10 md:max-w-[62%]">
                <p class="eyebrow">Next session</p>
                @if ($next)
                    <h2 class="mt-3 font-display text-4xl font-bold uppercase leading-none text-white sm:text-5xl">{{ $next->topic?->topic ?? $next->course?->objective }}</h2>
                    <p class="mt-3 text-ink-200">
                        {{ $next->schedule_date->isToday() ? 'Today' : ($next->schedule_date->isTomorrow() ? 'Tomorrow' : $next->schedule_date->format('l j F')) }}
                        · {{ \Carbon\Carbon::parse($next->time_start)->format('g:ia') }}–{{ \Carbon\Carbon::parse($next->time_stop)->format('g:ia') }}
                        · {{ ucfirst($next->session_type ?? 'theory') }} with {{ $next->instructor?->firstName }} {{ $next->instructor?->lastName }}
                    </p>
                    <a href="{{ route('student.schedule') }}" class="btn-brand btn-sm mt-6">Full timetable</a>
                @else
                    <h2 class="mt-3 font-display text-4xl font-bold uppercase leading-none text-white">No sessions this fortnight</h2>
                    <p class="mt-3 text-ink-300">Your instructor will publish your timetable here. Meanwhile, get ahead with your course materials.</p>
                    <a href="{{ route('student.materials') }}" class="btn-brand btn-sm mt-6">Open materials</a>
                @endif
            </div>
        </section>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-portal.stat label="Courses" :value="$enrolledCourses->count()" icon="book" :href="route('student.courses')" />
            <x-portal.stat label="Attendance" :value="$attendanceStats->total ? $attendancePercentage . '%' : '—'" icon="clipboard"
                :hint="$attendanceStats->total ? ($attendanceStats->present + $attendanceStats->late) . ' of ' . $attendanceStats->total . ' sessions attended' : 'No sessions recorded yet'"
                :tone="$attendanceStats->total && $attendancePercentage < 75 ? 'alert' : 'default'" :href="route('student.attendance')" />
            <x-portal.stat label="Upcoming sessions" :value="$upcomingSchedule->count()" icon="calendar" hint="In the next 14 days" :href="route('student.schedule')" />
            <x-portal.stat label="Unread announcements" :value="$unreadCount" icon="megaphone" :href="route('student.announcements')" />
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-3">
            {{-- Courses & instructors --}}
            <section class="portal-card xl:col-span-2">
                <div class="portal-card-header">
                    <h2 class="portal-card-title">My courses</h2>
                    <a href="{{ route('student.courses') }}" class="text-sm font-semibold text-ink-600 hover:text-ink-950">View all</a>
                </div>
                <ul class="divide-y divide-ink-100">
                    @forelse ($courseData as $item)
                        <li class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center">
                            @if ($item['course']->image_url)
                                <img src="{{ img($item['course']->image_url, 240) }}" alt="" class="h-16 w-24 shrink-0 rounded-lg object-cover">
                            @endif
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('student.course-details', $item['course']->id) }}" class="font-semibold text-ink-950 hover:underline">{{ $item['course']->objective }}</a>
                                <p class="text-sm text-ink-500">{{ $item['course']->duration }} weeks · {{ $item['theory_hours'] }} theory · {{ $item['practical_hours'] }} practical</p>
                            </div>
                            <div class="flex items-center gap-3 sm:w-56">
                                @if ($item['instructor'])
                                    <x-portal.avatar :user="$item['instructor']" size="h-9 w-9" />
                                    <div class="min-w-0 text-sm">
                                        <p class="truncate font-semibold text-ink-900">{{ $item['instructor']->firstName }} {{ $item['instructor']->lastName }}</p>
                                        <p class="text-xs text-ink-500">Your instructor</p>
                                    </div>
                                @else
                                    <span class="badge-yellow">Instructor being assigned</span>
                                @endif
                            </div>
                        </li>
                    @empty
                        <li><x-portal.empty icon="book" title="No courses yet" /></li>
                    @endforelse
                </ul>
            </section>

            {{-- Announcements --}}
            <section class="portal-card">
                <div class="portal-card-header">
                    <h2 class="portal-card-title">Announcements</h2>
                    <a href="{{ route('student.announcements') }}" class="text-sm font-semibold text-ink-600 hover:text-ink-950">All</a>
                </div>
                <ul class="divide-y divide-ink-100">
                    @forelse ($announcements as $announcement)
                        <li>
                            <a href="{{ route('student.announcement.show', $announcement->id) }}" class="flex gap-3 px-5 py-3 hover:bg-ink-50">
                                <span @class(['mt-1.5 h-2 w-2 shrink-0 rounded-full', 'bg-brand-500' => ! $announcement->is_read, 'bg-transparent' => $announcement->is_read])></span>
                                <span class="min-w-0">
                                    <span @class(['block truncate text-sm', 'font-semibold text-ink-950' => ! $announcement->is_read, 'text-ink-700' => $announcement->is_read])>{{ $announcement->title }}</span>
                                    <span class="block text-xs text-ink-400">{{ $announcement->created_at->diffForHumans() }}@if ($announcement->priority === 'high') · <span class="font-semibold text-red-600">Important</span>@endif</span>
                                </span>
                            </a>
                        </li>
                    @empty
                        <li><x-portal.empty icon="megaphone" title="Nothing new" text="School and course announcements will appear here." /></li>
                    @endforelse
                </ul>
            </section>

            {{-- Upcoming --}}
            <section class="portal-card xl:col-span-2">
                <div class="portal-card-header">
                    <h2 class="portal-card-title">Coming up</h2>
                    <a href="{{ route('student.schedule') }}" class="text-sm font-semibold text-ink-600 hover:text-ink-950">Timetable</a>
                </div>
                <ul class="divide-y divide-ink-100">
                    @forelse ($upcomingSchedule as $session)
                        <li class="flex items-center gap-4 px-5 py-3">
                            <div class="w-14 shrink-0 rounded-lg bg-ink-50 py-1.5 text-center">
                                <p class="text-xs font-semibold uppercase text-ink-400">{{ $session->schedule_date->format('M') }}</p>
                                <p class="font-display text-2xl font-bold leading-none text-ink-950">{{ $session->schedule_date->format('j') }}</p>
                            </div>
                            <div class="min-w-0 flex-1 text-sm">
                                <p class="truncate font-semibold text-ink-950">{{ $session->topic?->topic ?? 'Training session' }}</p>
                                <p class="truncate text-xs text-ink-500">{{ $session->course?->objective }} · {{ $session->instructor?->firstName }}</p>
                            </div>
                            <div class="text-right text-sm">
                                <p class="font-semibold text-ink-900">{{ \Carbon\Carbon::parse($session->time_start)->format('g:ia') }}</p>
                                <span class="badge-grey">{{ ucfirst($session->session_type ?? 'theory') }}</span>
                            </div>
                        </li>
                    @empty
                        <li><x-portal.empty icon="calendar" title="Nothing scheduled yet" /></li>
                    @endforelse
                </ul>
            </section>

            {{-- Materials --}}
            <section class="portal-card">
                <div class="portal-card-header">
                    <h2 class="portal-card-title">New materials</h2>
                    <a href="{{ route('student.materials') }}" class="text-sm font-semibold text-ink-600 hover:text-ink-950">All</a>
                </div>
                <ul class="divide-y divide-ink-100">
                    @forelse ($courseMaterials->take(5) as $material)
                        <li>
                            <a href="{{ route('student.download-material', $material->id) }}" class="flex items-center gap-3 px-5 py-3 hover:bg-ink-50">
                                <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-ink-950 text-[10px] font-bold uppercase text-brand-400">{{ \Illuminate\Support\Str::limit($material->file_type ?: pathinfo($material->file_url, PATHINFO_EXTENSION), 4, '') }}</span>
                                <span class="min-w-0">
                                    <span class="block truncate text-sm font-semibold text-ink-900">{{ $material->file_name }}</span>
                                    <span class="block truncate text-xs text-ink-400">{{ $material->course_name }}</span>
                                </span>
                                <x-site.icon name="download" class="ml-auto h-4 w-4 shrink-0 text-ink-400" />
                            </a>
                        </li>
                    @empty
                        <li><x-portal.empty icon="book" title="No materials yet" /></li>
                    @endforelse
                </ul>
            </section>
        </div>
    </x-portal.page>
</x-app-layout>
