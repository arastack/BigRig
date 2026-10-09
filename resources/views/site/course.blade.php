@php
    $currency = $settings->base_currency ?? '₦';
    $requirements = $course->requirementList();
    $vat = round($course->price * \App\Support\Pricing::vatRate(), 2);
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Course',
        'name' => $course->objective,
        'description' => \Illuminate\Support\Str::limit(strip_tags($course->course_details), 300),
        'provider' => ['@type' => 'Organization', 'name' => $settings->site_name ?? config('app.name'), 'sameAs' => url('/')],
        'offers' => ['@type' => 'Offer', 'price' => (float) $course->price, 'priceCurrency' => 'NGN', 'category' => 'Paid'],
        'hasCourseInstance' => ['@type' => 'CourseInstance', 'courseMode' => 'onsite', 'courseWorkload' => 'P' . (int) $course->duration . 'W'],
    ];
@endphp
<x-site.layout :title="$course->objective" :description="\Illuminate\Support\Str::limit(strip_tags($course->course_details), 160)" :image="img($course->image_url, 1200)" :schema="$schema">

    <x-site.page-hero :eyebrow="page_text('course.hero.eyebrow', [':weeks' => $course->duration])" :title="$course->objective" :image="img($course->image_url, 1600)"
        :crumbs="[page_text('nav.courses') => route('courses'), $course->objective => null]">
        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
            <a href="{{ route('register', ['course' => $course->id]) }}" class="btn-brand">{{ page_text('course.hero.button_primary') }} <x-site.icon name="arrow-right" class="h-5 w-5" /></a>
            <a href="{{ route('contact') }}" class="btn-outline-light">{{ page_text('course.hero.button_secondary') }}</a>
        </div>
    </x-site.page-hero>

    <section class="section">
        <div class="container-site grid gap-12 lg:grid-cols-12">
            {{-- Main content --}}
            <div class="lg:col-span-8">
                <div class="prose-site text-ink-600">{!! clean_html($course->course_details) !!}</div>

                @if ($course->youtubeId())
                    <div class="mt-12">
                        <h2 class="text-3xl">{{ page_text('course.video.title') }}</h2>
                        <div class="mt-5 aspect-video overflow-hidden rounded-lg bg-ink-950" x-data="{ play: false }">
                            <template x-if="play">
                                <iframe class="h-full w-full" src="https://www.youtube-nocookie.com/embed/{{ $course->youtubeId() }}?autoplay=1&rel=0" title="{{ $course->objective }} video" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>
                            </template>
                            <button x-show="!play" type="button" @click="play = true" class="group relative h-full w-full" aria-label="Play video">
                                <img src="{{ $course->video_thumbnail_url ? img($course->video_thumbnail_url, 1200) : 'https://i.ytimg.com/vi/' . $course->youtubeId() . '/hqdefault.jpg' }}" alt="" class="h-full w-full object-cover opacity-80" loading="lazy">
                                <span class="absolute left-1/2 top-1/2 inline-flex h-20 w-20 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full bg-brand-400 text-ink-950 shadow-xl transition group-hover:scale-110"><x-site.icon name="play" class="ml-1 h-8 w-8 fill-current" /></span>
                            </button>
                        </div>
                    </div>
                @endif

                @if ($course->curriculum->isNotEmpty())
                    <div class="mt-14">
                        <p class="eyebrow">{{ page_text('course.curriculum.eyebrow') }}</p>
                        <h2 class="mt-3 text-4xl">{{ page_text('course.curriculum.title') }}</h2>
                        <ol class="mt-8 space-y-3">
                            @foreach ($course->curriculum as $n => $topic)
                                <li class="flex gap-5 rounded-lg border border-ink-100 p-5">
                                    <span class="font-display text-3xl font-extrabold text-brand-600">{{ str_pad($n + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                    <div>
                                        <h3 class="text-xl normal-case tracking-normal font-inter font-semibold">{{ $topic->topic }}</h3>
                                        @if ($topic->summary)<p class="mt-1 text-ink-500">{{ strip_tags($topic->summary) }}</p>@endif
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <aside class="lg:col-span-4">
                <div class="sticky top-28 space-y-6">
                    <div class="overflow-hidden rounded-lg border-2 border-ink-950">
                        <div class="bg-ink-950 p-6 text-white">
                            <p class="text-sm font-semibold uppercase tracking-wider text-ink-300">{{ page_text('course.sidebar.tuition') }}</p>
                            <p class="font-display text-5xl font-extrabold text-brand-400">{{ $currency }}{{ number_format($course->price) }}</p>
                            <p class="mt-1 text-sm text-ink-300">{{ page_text('course.sidebar.vat', [':amount' => $currency . number_format($vat, 2), ':vat' => \App\Support\Pricing::vatLabel()]) }}</p>
                        </div>
                        <dl class="divide-y divide-ink-100">
                            @foreach ([
                                ['calendar', page_text('course.sidebar.duration'), page_text('course.sidebar.weeks', [':weeks' => $course->duration])],
                                ['book', page_text('course.sidebar.theory'), $course->theory_session ?: '—'],
                                ['steering', page_text('course.sidebar.practical'), $course->practical_session ?: '—'],
                                ['clipboard', page_text('course.sidebar.examination'), $course->examination ?: page_text('course.sidebar.exam_default')],
                                ['award', page_text('course.sidebar.certificate_label'), page_text('course.sidebar.certificate')],
                            ] as [$icon, $label, $value])
                                <div class="flex items-center justify-between gap-4 px-6 py-3.5">
                                    <dt class="flex items-center gap-3 text-ink-500"><x-site.icon :name="$icon" class="h-5 w-5 text-ink-400" />{{ $label }}</dt>
                                    <dd class="text-right font-semibold text-ink-950">{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                        <div class="space-y-3 p-6 pt-2">
                            <a href="{{ route('register', ['course' => $course->id]) }}" class="btn-brand w-full">{{ page_text('course.sidebar.apply') }}</a>
                            <a href="{{ route('register.form-pdf') }}" class="btn-outline w-full"><x-site.icon name="download" class="h-5 w-5" /> {{ page_text('course.sidebar.paper') }}</a>
                            @if ($activeBatch)
                                <p class="text-center text-sm text-ink-500">{{ page_text('course.sidebar.enrolling') }} <strong class="text-ink-900">{{ $activeBatch->batch_name }}</strong></p>
                            @endif
                        </div>
                    </div>

                    @if ($requirements)
                        <div class="rounded-lg bg-ink-50 p-6">
                            <h2 class="text-2xl">{{ page_text('course.requirements.title') }}</h2>
                            <ul class="mt-4 space-y-3">
                                @foreach ($requirements as $requirement)
                                    <li class="flex gap-3 text-ink-700"><x-site.icon name="check-circle" class="mt-0.5 h-5 w-5 shrink-0 text-ink-950" />{{ $requirement }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </aside>
        </div>
    </section>

    @if ($faqs->isNotEmpty())
        <section class="section bg-ink-50">
            <div class="container-site grid gap-12 lg:grid-cols-12">
                <div class="lg:col-span-4">
                    <p class="eyebrow">{{ page_text('course.faq.eyebrow') }}</p>
                    <h2 class="section-title">{{ page_text('course.faq.title') }}</h2>
                </div>
                <div class="lg:col-span-8"><x-site.faq-list :faqs="$faqs" /></div>
            </div>
        </section>
    @endif

    @if ($otherCourses->isNotEmpty())
        <section class="section">
            <div class="container-site">
                <h2 class="text-4xl">{{ page_text('course.other.title') }}</h2>
                <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($otherCourses->take(3) as $other)
                        <x-site.course-card :course="$other" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <x-site.apply-cta :title="page_text('course.cta.title', [':course' => $course->objective])" />
</x-site.layout>
