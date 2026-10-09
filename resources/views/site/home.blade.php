<x-site.layout :description="$settings->site_description">
    @php
        $currency = $settings->base_currency ?? '₦';
        $slides = $sliders->filter(fn ($s) => $s->image_url)->values();
        $stats = collect([
            ['value' => $about?->students_count ? number_format($about->students_count) . '+' : null, 'label' => page_text('home.stats.1')],
            ['value' => $about?->pass_rate ? rtrim(rtrim(number_format($about->pass_rate, 1), '0'), '.') . '%' : null, 'label' => page_text('home.stats.2')],
            ['value' => $about?->instructors_count ?: null, 'label' => page_text('home.stats.3')],
            ['value' => $courses->count() ?: null, 'label' => page_text('home.stats.4')],
        ])->filter(fn ($s) => $s['value']);
    @endphp

    {{-- Hero --}}
    <section class="on-dark relative isolate overflow-hidden bg-ink-950 text-white"
             x-data="{ i: 0, n: {{ max($slides->count(), 1) }}, timer: null, start() { if (this.n > 1 && ! window.matchMedia('(prefers-reduced-motion: reduce)').matches) this.timer = setInterval(() => this.i = (this.i + 1) % this.n, 6500) }, go(k) { this.i = k; clearInterval(this.timer); this.start() } }"
             x-init="start()">
        <div class="pointer-events-none absolute inset-0 -z-10 opacity-[0.07]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 28px 28px;" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -right-16 top-0 -z-10 h-full w-[46%] skew-x-[-12deg] bg-brand-400 max-lg:hidden" aria-hidden="true"></div>

        <div class="container-site grid items-center gap-10 pb-28 pt-14 lg:min-h-[640px] lg:grid-cols-12 lg:pb-32 lg:pt-20">
            <div class="relative z-10 lg:col-span-6">
                <p class="eyebrow">{{ page_text('home.hero.eyebrow') }}</p>
                @forelse ($slides as $k => $slide)
                    <div x-show="i === {{ $k }}" @if ($k > 0) x-cloak @endif
                         x-transition:enter="transition duration-500 ease-out" x-transition:enter-start="opacity-0 translate-y-3" x-transition:enter-end="opacity-100 translate-y-0">
                        @if ($k === 0)
                            <h1 class="mt-5 text-6xl text-white sm:text-7xl xl:text-8xl">{{ $slide->title }}</h1>
                        @else
                            <p class="mt-5 font-display text-6xl font-bold uppercase leading-[0.95] tracking-tight text-white sm:text-7xl xl:text-8xl" role="heading" aria-level="2">{{ $slide->title }}</p>
                        @endif
                        <p class="mt-6 max-w-md text-lg leading-relaxed text-ink-200">{{ \Illuminate\Support\Str::limit(strip_tags($slide->text), 220) }}</p>
                    </div>
                @empty
                    <h1 class="mt-5 text-6xl text-white sm:text-7xl xl:text-8xl">{{ page_text('home.hero.title') }}</h1>
                @endforelse

                <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('register') }}" class="btn-brand text-lg">{{ page_text('home.hero.button_primary') }} <x-site.icon name="arrow-right" class="h-5 w-5" /></a>
                    <a href="{{ route('courses') }}" class="btn-outline-light text-lg">{{ page_text('home.hero.button_secondary') }}</a>
                </div>

                @if ($slides->count() > 1)
                    <div class="mt-10 flex items-center gap-2" role="tablist" aria-label="Highlights">
                        @foreach ($slides as $k => $slide)
                            <button type="button" class="h-1.5 rounded-full transition-all" :class="i === {{ $k }} ? 'w-10 bg-brand-400' : 'w-5 bg-white/30 hover:bg-white/60'" @click="go({{ $k }})" role="tab" :aria-selected="(i === {{ $k }}).toString()" aria-label="Show highlight {{ $k + 1 }}"></button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="relative lg:col-span-6">
                <div class="relative mx-auto aspect-[4/3] w-full max-w-2xl">
                    @foreach ($slides as $k => $slide)
                        <img src="{{ img($slide->image_url, 1100) }}" alt="" x-show="i === {{ $k }}" @if ($k > 0) x-cloak @else fetchpriority="high" @endif
                             x-transition:enter="transition duration-700 ease-out" x-transition:enter-start="opacity-0 translate-x-10" x-transition:enter-end="opacity-100 translate-x-0"
                             class="absolute inset-0 h-full w-full object-contain drop-shadow-[0_30px_40px_rgba(0,0,0,0.45)]" width="1100" height="825">
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Road --}}
        <div class="absolute inset-x-0 bottom-0 h-16 bg-ink-900" aria-hidden="true"><div class="lane absolute inset-x-0 top-1/2 -translate-y-1/2"></div></div>
    </section>

    {{-- Stats --}}
    @if ($stats->isNotEmpty())
        <section aria-label="BigRig in numbers" class="border-b border-ink-100 bg-white">
            <dl class="container-site grid grid-cols-2 divide-ink-100 lg:grid-cols-4 lg:divide-x">
                @foreach ($stats as $stat)
                    <div class="px-2 py-8 text-center sm:py-10">
                        <dt class="text-sm font-medium text-ink-500">{{ $stat['label'] }}</dt>
                        <dd class="order-first font-display text-5xl font-extrabold text-ink-950 sm:text-6xl">{{ $stat['value'] }}</dd>
                    </div>
                @endforeach
            </dl>
        </section>
    @endif

    {{-- Courses --}}
    <section class="section bg-ink-50">
        <div class="container-site">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-2xl">
                    <p class="eyebrow">{{ page_text('home.courses.eyebrow') }}</p>
                    <h2 class="section-title">{{ page_text('home.courses.title') }}</h2>
                    <p class="lead mt-4">{{ page_text('home.courses.lead') }}</p>
                </div>
                <a href="{{ route('courses') }}" class="link-underline shrink-0">{{ page_text('home.courses.link') }}</a>
            </div>
            <div class="mt-12 grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($courses as $course)
                    <x-site.course-card :course="$course" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- Admission steps --}}
    <section class="section on-dark relative overflow-hidden bg-ink-950 text-white">
        <div class="container-site">
            <div class="grid gap-12 lg:grid-cols-12">
                <div class="lg:col-span-4">
                    <p class="eyebrow">{{ page_text('home.steps.eyebrow') }}</p>
                    <h2 class="section-title text-white">{{ page_text('home.steps.title') }}</h2>
                    <p class="mt-5 text-lg leading-relaxed text-ink-300">{{ page_text('home.steps.lead') }}</p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row lg:flex-col xl:flex-row">
                        <a href="{{ route('register') }}" class="btn-brand">{{ page_text('home.steps.button_primary') }}</a>
                        <a href="{{ route('register.form-pdf') }}" class="btn-outline-light"><x-site.icon name="download" class="h-5 w-5" /> {{ page_text('home.steps.button_secondary') }}</a>
                    </div>
                </div>
                <ol class="relative grid gap-6 sm:grid-cols-2 lg:col-span-8">
                    @foreach ([
                        ['icon' => 'steering', 'title' => page_text('home.steps.1.title'), 'text' => page_text('home.steps.1.text')],
                        ['icon' => 'file-text', 'title' => page_text('home.steps.2.title'), 'text' => page_text('home.steps.2.text')],
                        ['icon' => 'credit-card', 'title' => page_text('home.steps.3.title'), 'text' => page_text('home.steps.3.text')],
                        ['icon' => 'truck', 'title' => page_text('home.steps.4.title'), 'text' => page_text('home.steps.4.text')],
                    ] as $n => $step)
                        <li class="relative rounded-lg bg-white/[0.04] p-6 ring-1 ring-white/10">
                            <div class="flex items-center justify-between">
                                <span class="inline-flex h-12 w-12 items-center justify-center rounded-md bg-brand-400 text-ink-950"><x-site.icon :name="$step['icon']" class="h-6 w-6" /></span>
                                <span class="font-display text-6xl font-extrabold text-white/10">0{{ $n + 1 }}</span>
                            </div>
                            <h3 class="mt-5 text-2xl text-white">{{ $step['title'] }}</h3>
                            <p class="mt-2 text-ink-300">{{ $step['text'] }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </section>

    {{-- Why BigRig --}}
    <section class="section">
        <div class="container-site grid gap-12 lg:grid-cols-12 lg:gap-16">
            <div class="lg:col-span-5">
                <div class="relative">
                    <img src="{{ img($courses->sortByDesc('price')->first()?->image_url, 900) }}" alt="A BigRig training truck" class="aspect-[4/5] w-full rounded-lg object-cover" loading="lazy" width="900" height="1125">
                    <div class="absolute -bottom-6 -right-4 max-w-[15rem] rounded-lg bg-brand-400 p-5 shadow-[6px_6px_0_0_theme(colors.ink.950)] sm:-right-6">
                        <p class="font-display text-5xl font-extrabold leading-none text-ink-950">{{ $courses->min('duration') }}–{{ $courses->max('duration') }}</p>
                        <p class="mt-1 text-sm font-semibold text-ink-900">{{ page_text('home.why.badge') }}</p>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-7">
                <p class="eyebrow">{{ page_text('home.why.eyebrow') }}</p>
                <h2 class="section-title">{{ $about?->history_title ?? page_text('home.why.title') }}</h2>
                <div class="prose-site mt-6 text-ink-600">{!! clean_html(\Illuminate\Support\Str::limit($about?->mission_statement ?? '', 600)) !!}</div>
                <ul class="mt-8 grid gap-4 sm:grid-cols-2">
                    @foreach ([page_text('home.why.points.1'), page_text('home.why.points.2'), page_text('home.why.points.3'), page_text('home.why.points.4')] as $point)
                        <li class="flex gap-3"><span class="mt-0.5 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-400"><x-site.icon name="check" class="h-4 w-4 text-ink-950" /></span><span class="font-medium text-ink-800">{{ $point }}</span></li>
                    @endforeach
                </ul>
                <a href="{{ route('about-us') }}" class="btn-outline mt-10">{{ page_text('home.why.button') }}</a>
            </div>
        </div>
    </section>

    {{-- Services --}}
    @if ($services->isNotEmpty())
        <section class="section bg-ink-50">
            <div class="container-site">
                <div class="max-w-2xl">
                    <p class="eyebrow">{{ page_text('home.services.eyebrow') }}</p>
                    <h2 class="section-title">{{ page_text('home.services.title') }}</h2>
                </div>
                <div class="mt-12 grid gap-px overflow-hidden rounded-lg border border-ink-100 bg-ink-100 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($services as $service)
                        <div class="group bg-white p-7 transition hover:bg-ink-950">
                            <span class="inline-flex h-12 w-12 items-center justify-center rounded-md bg-ink-950 text-brand-400 transition group-hover:bg-brand-400 group-hover:text-ink-950"><x-site.service-icon :name="$service->service_name" /></span>
                            <h3 class="mt-5 text-2xl transition group-hover:text-white">{{ $service->service_name }}</h3>
                            <p class="mt-2 text-ink-500 transition group-hover:text-ink-300">{{ \Illuminate\Support\Str::limit(strip_tags($service->service_description), 160) }}</p>
                        </div>
                    @endforeach
                    @if ($services->count() % 3 !== 0)
                        <a href="{{ route('contact') }}" @class([
                            'group flex flex-col justify-between bg-brand-400 p-7',
                            'sm:col-span-2' => $services->count() % 2 === 0,
                            'lg:col-span-1' => $services->count() % 3 === 2,
                            'lg:col-span-2' => $services->count() % 3 === 1,
                        ])>
                            <h3 class="text-3xl">{{ page_text('home.services.cta_title') }}</h3>
                            <p class="mt-3 max-w-md text-ink-800">{{ page_text('home.services.cta_text') }}</p>
                            <span class="mt-6 inline-flex items-center gap-2 font-display text-lg font-bold uppercase tracking-wide text-ink-950">{{ page_text('home.services.cta_link') }} <x-site.icon name="arrow-right" class="h-5 w-5 transition group-hover:translate-x-1" /></span>
                        </a>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- Founder --}}
    @if ($founder && $founder->show_founder)
        <section class="section">
            <div class="container-site grid items-center gap-12 lg:grid-cols-12">
                <div class="lg:col-span-4">
                    <img src="{{ img($founder->founder_picture, 700) }}" alt="{{ $founder->founder_name }}" class="aspect-square w-full rounded-lg object-cover grayscale" loading="lazy">
                </div>
                <figure class="lg:col-span-8">
                    <x-site.icon name="quote" class="h-10 w-10 text-brand-500" />
                    <h2 class="mt-4 text-4xl">{{ $founder->speech_title }}</h2>
                    <blockquote class="prose-site mt-6 text-ink-600">{!! clean_html($founder->speech_content) !!}</blockquote>
                    <figcaption class="mt-6 font-display text-xl font-bold uppercase text-ink-950">{{ $founder->founder_name }} <span class="block text-sm font-semibold tracking-widest text-ink-400">{{ page_text('about.founder.caption') }}</span></figcaption>
                </figure>
            </div>
        </section>
    @endif

    {{-- Team --}}
    @if ($team->isNotEmpty())
        <section class="section">
            <div class="container-site">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-2xl">
                        <p class="eyebrow">{{ page_text('home.team.eyebrow') }}</p>
                        <h2 class="section-title">{{ page_text('home.team.title') }}</h2>
                    </div>
                    <a href="{{ route('our-instructors') }}" class="link-underline shrink-0">{{ page_text('home.team.link') }}</a>
                </div>
                <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($team as $member)
                        @include('site.partials.team-card', ['member' => $member])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Testimonials --}}
    @if ($testimonials->isNotEmpty())
        <section class="section on-dark bg-ink-950 text-white">
            <div class="container-site">
                <div class="max-w-2xl">
                    <p class="eyebrow">{{ page_text('home.testimonials.eyebrow') }}</p>
                    <h2 class="section-title text-white">{{ page_text('home.testimonials.title') }}</h2>
                </div>
                <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($testimonials as $testimonial)
                        <figure class="flex flex-col rounded-lg bg-white/[0.04] p-7 ring-1 ring-white/10">
                            <div class="flex gap-1 text-brand-400" aria-label="Rated {{ $testimonial->rating }} out of 5">
                                @for ($s = 1; $s <= 5; $s++)
                                    <x-site.icon name="star" @class(['h-5 w-5', 'fill-current' => $s <= $testimonial->rating, 'opacity-30' => $s > $testimonial->rating]) />
                                @endfor
                            </div>
                            <blockquote class="mt-5 flex-1 text-lg leading-relaxed text-ink-100">{{ \Illuminate\Support\Str::limit(html_entity_decode(strip_tags($testimonial->testimony)), 320) }}</blockquote>
                            <figcaption class="mt-6 flex items-center gap-3">
                                @if ($testimonial->image_url)
                                    <img src="{{ img($testimonial->image_url, 120) }}" alt="" class="h-12 w-12 rounded-full object-cover" loading="lazy">
                                @else
                                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-brand-400 font-display text-xl font-bold text-ink-950">{{ mb_substr($testimonial->full_name, 0, 1) }}</span>
                                @endif
                                <span class="font-display text-lg font-bold uppercase tracking-wide text-white">{{ $testimonial->full_name }}</span>
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- FAQ --}}
    @if ($faqs->isNotEmpty())
        <section class="section">
            <div class="container-site grid gap-12 lg:grid-cols-12">
                <div class="lg:col-span-4">
                    <p class="eyebrow">{{ page_text('home.faq.eyebrow') }}</p>
                    <h2 class="section-title">{{ page_text('home.faq.title') }}</h2>
                    <p class="lead mt-4">{{ page_text('home.faq.lead_before') }} <a href="{{ route('contact') }}" class="link-underline">{{ page_text('home.faq.lead_link') }}</a> {{ page_text('home.faq.lead_after') }}</p>
                </div>
                <div class="lg:col-span-8">
                    <x-site.faq-list :faqs="$faqs" />
                    <a href="{{ route('faq') }}" class="link-underline mt-6 inline-block">{{ page_text('home.faq.all_link') }}</a>
                </div>
            </div>
        </section>
    @endif

    {{-- Partners --}}
    @if ($partners->isNotEmpty())
        <section class="border-t border-ink-100 py-12">
            <div class="container-site flex flex-col items-center gap-8 lg:flex-row">
                <p class="eyebrow shrink-0">{{ page_text('home.partners.eyebrow') }}</p>
                <ul class="flex flex-wrap items-center justify-center gap-x-12 gap-y-6">
                    @foreach ($partners as $partner)
                        <li><img src="{{ img($partner->logo, 300) }}" alt="{{ $partner->name }}" class="h-12 w-auto grayscale transition hover:grayscale-0" loading="lazy"></li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    <x-site.apply-cta />
</x-site.layout>
