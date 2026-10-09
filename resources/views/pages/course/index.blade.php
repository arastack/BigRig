@section('title', 'Courses')
@php $currency = $settings->base_currency ?? '₦'; @endphp
<x-app-layout>
    <x-portal.page title="Courses" subtitle="Curriculum, materials, enrolled students and upcoming sessions for every programme.">
        <x-slot:actions>
            @can('manage_course_catalogue')
                <a href="{{ route('objectives') }}" class="btn-ghost btn-sm"><x-site.icon name="pencil" class="h-4 w-4" /> Courses &amp; fees</a>
            @endcan
            @can('read_training_schedule')
                <a href="{{ route('schedule') }}" class="btn-dark btn-sm"><x-site.icon name="calendar" class="h-4 w-4" /> Training schedule</a>
            @endcan
        </x-slot:actions>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-portal.stat label="Courses" :value="$courses->count()" icon="book" />
            <x-portal.stat label="Students in training" :value="$enrolled->sum()" icon="graduation" hint="Across all courses" />
            <x-portal.stat label="Curriculum topics" :value="$courses->sum('curriculum_count')" icon="clipboard" />
            <x-portal.stat label="Sessions in the next 7 days" :value="$upcoming->sum()" icon="calendar" />
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-3">
            <section class="xl:col-span-2">
                @if ($courses->isEmpty())
                    <div class="portal-card"><x-portal.empty icon="book" title="No courses yet" text="Add your first course under Courses & fees." /></div>
                @else
                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach ($courses as $course)
                            <a href="{{ route('course-details', $course->id) }}" class="portal-card group flex flex-col overflow-hidden transition hover:border-ink-950 hover:shadow-[4px_4px_0_0_theme(colors.brand.400)]">
                                <div class="relative aspect-[16/7] bg-ink-100">
                                    @if ($course->image_url)
                                        <img src="{{ img($course->image_url, 640) }}" alt="" class="h-full w-full object-cover" loading="lazy">
                                    @endif
                                    <span class="badge-dark absolute left-3 top-3">{{ $course->duration }} weeks</span>
                                </div>
                                <div class="flex flex-1 flex-col p-5">
                                    <h2 class="font-display text-2xl font-bold uppercase leading-tight text-ink-950 group-hover:underline">{{ $course->objective }}</h2>
                                    <p class="mt-1 text-sm text-ink-500">{{ $currency }}{{ number_format($course->price) }} · {{ $course->theory_session ?: 0 }} theory · {{ $course->practical_session ?: 0 }} practical</p>
                                    <dl class="mt-4 grid grid-cols-4 gap-2 border-t border-ink-100 pt-4 text-center">
                                        @foreach ([
                                            'Students' => $enrolled[$course->id] ?? 0,
                                            'Topics' => $course->curriculum_count,
                                            'Files' => $course->materials_count,
                                            'Next 7d' => $upcoming[$course->id] ?? 0,
                                        ] as $label => $value)
                                            <div>
                                                <dt class="text-[11px] font-semibold uppercase tracking-wider text-ink-400">{{ $label }}</dt>
                                                <dd class="font-display text-2xl font-bold text-ink-950">{{ $value }}</dd>
                                            </div>
                                        @endforeach
                                    </dl>
                                    @if ($course->curriculum_count === 0)
                                        <p class="mt-3 flex items-center gap-1.5 text-xs font-semibold text-brand-800"><x-site.icon name="alert" class="h-3.5 w-3.5" /> No curriculum yet — sessions need topics</p>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>

            <section class="portal-card self-start">
                <div class="portal-card-header">
                    <h2 class="portal-card-title">Next 7 days</h2>
                    @can('read_training_schedule')<a href="{{ route('schedule') }}" class="text-sm font-semibold text-ink-600 hover:text-ink-950">All sessions</a>@endcan
                </div>
                <ul class="divide-y divide-ink-100">
                    @forelse ($thisWeek as $session)
                        <li class="flex gap-4 px-5 py-3">
                            <div class="w-12 shrink-0 text-center">
                                <p class="text-xs font-semibold uppercase text-ink-400">{{ $session->schedule_date->format('D') }}</p>
                                <p class="font-display text-2xl font-bold leading-none text-ink-950">{{ $session->schedule_date->format('j') }}</p>
                            </div>
                            <div class="min-w-0 text-sm">
                                <p class="truncate font-semibold text-ink-950">{{ $session->topic?->topic ?? 'Training session' }}</p>
                                <p class="truncate text-xs text-ink-500">{{ $session->course?->objective }}</p>
                                <p class="text-xs text-ink-400">{{ \Carbon\Carbon::parse($session->time_start)->format('g:ia') }} · {{ $session->instructor?->firstName }} · {{ count($session->studentIds()) }} students</p>
                            </div>
                        </li>
                    @empty
                        <li><x-portal.empty icon="calendar" title="Nothing scheduled" text="No sessions in the next week." /></li>
                    @endforelse
                </ul>
            </section>
        </div>
    </x-portal.page>
</x-app-layout>
