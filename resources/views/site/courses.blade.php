<x-site.layout :title="page_text('courses.meta.title')" :description="page_text('courses.meta.description')">
    @php $currency = $settings->base_currency ?? '₦'; @endphp

    <x-site.page-hero :eyebrow="page_text('courses.hero.eyebrow')" :title="page_text('courses.hero.title')" :crumbs="['Courses' => null]"
        :lead="page_text('courses.hero.lead')" />

    <section class="section bg-ink-50">
        <div class="container-site">
            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($courses as $course)
                    <x-site.course-card :course="$course" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- Comparison table --}}
    <section class="section">
        <div class="container-site">
            <div class="max-w-2xl">
                <p class="eyebrow">{{ page_text('courses.compare.eyebrow') }}</p>
                <h2 class="section-title">{{ page_text('courses.compare.title') }}</h2>
                <p class="lead mt-4">{{ page_text('courses.compare.lead', [':vat' => \App\Support\Pricing::vatLabel()]) }}</p>
            </div>

            <div class="mt-10 overflow-x-auto rounded-lg border border-ink-100">
                <table class="w-full min-w-[720px] text-left">
                    <thead class="bg-ink-950 text-white">
                        <tr>
                            <th scope="col" class="px-5 py-4 font-display text-base font-semibold uppercase tracking-wider">Programme</th>
                            <th scope="col" class="px-5 py-4 font-display text-base font-semibold uppercase tracking-wider">Duration</th>
                            <th scope="col" class="px-5 py-4 font-display text-base font-semibold uppercase tracking-wider">Theory</th>
                            <th scope="col" class="px-5 py-4 font-display text-base font-semibold uppercase tracking-wider">Practical</th>
                            <th scope="col" class="px-5 py-4 font-display text-base font-semibold uppercase tracking-wider">Exam</th>
                            <th scope="col" class="px-5 py-4 text-right font-display text-base font-semibold uppercase tracking-wider">Tuition</th>
                            <th scope="col" class="px-5 py-4"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-100">
                        @foreach ($courses as $course)
                            <tr class="hover:bg-ink-50">
                                <th scope="row" class="px-5 py-4"><a href="{{ route('course', $course->slug) }}" class="font-semibold text-ink-950 hover:underline">{{ $course->objective }}</a></th>
                                <td class="px-5 py-4">{{ $course->duration }} weeks</td>
                                <td class="px-5 py-4">{{ $course->theory_session ?: '—' }} sessions</td>
                                <td class="px-5 py-4">{{ $course->practical_session ?: '—' }} sessions</td>
                                <td class="px-5 py-4">{{ $course->examination ?: 'Included' }}</td>
                                <td class="px-5 py-4 text-right font-display text-xl font-bold text-ink-950">{{ $currency }}{{ number_format($course->price) }}</td>
                                <td class="px-5 py-4 text-right"><a href="{{ route('register', ['course' => $course->id]) }}" class="btn-brand btn-sm">Apply</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @foreach (\App\Support\Pricing::packages() as $combo)
                <div class="mt-6 flex flex-col gap-4 rounded-lg bg-brand-400 p-6 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="font-display text-3xl font-bold uppercase text-ink-950">{{ $combo['name'] }} — save {{ $currency }}{{ number_format($combo['discount']) }}</p>
                        <p class="mt-1 text-ink-800">{{ $combo['description'] }}@if ($combo['weeks']) Complete both in about {{ $combo['weeks'] }} weeks.@endif</p>
                    </div>
                    <a href="{{ route('register') }}" class="btn-dark shrink-0">{{ page_text('courses.combo.button') }}</a>
                </div>
            @endforeach
        </div>
    </section>

    @if ($services->isNotEmpty())
        <section class="section bg-ink-50">
            <div class="container-site">
                <div class="max-w-2xl">
                    <p class="eyebrow">{{ page_text('courses.services.eyebrow') }}</p>
                    <h2 class="section-title">{{ page_text('courses.services.title') }}</h2>
                    <p class="lead mt-4">{{ page_text('courses.services.lead') }}</p>
                </div>
                <ul class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($services as $service)
                        <li class="flex gap-4 rounded-lg border border-ink-100 bg-white p-5">
                            <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-md bg-ink-950 text-brand-400"><x-site.service-icon :name="$service->service_name" class="h-5 w-5" /></span>
                            <div>
                                <h3 class="text-xl">{{ $service->service_name }}</h3>
                                <p class="mt-1 text-sm text-ink-500">{{ \Illuminate\Support\Str::limit(strip_tags($service->service_description), 140) }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    <x-site.apply-cta />
</x-site.layout>
