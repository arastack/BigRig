<x-site.layout :title="page_text('about.meta.title')" :description="\Illuminate\Support\Str::limit(strip_tags($about?->mission_statement ?? ''), 160)">
    @php
        $stats = collect([
            ['value' => $about?->students_count ? number_format($about->students_count) . '+' : null, 'label' => page_text('about.stats.1')],
            ['value' => $about?->pass_rate ? rtrim(rtrim(number_format($about->pass_rate, 1), '0'), '.') . '%' : null, 'label' => page_text('about.stats.2')],
            ['value' => $about?->instructors_count ?: null, 'label' => page_text('about.stats.3')],
            ['value' => $settings?->commence_year ? page_text('about.stats.since', [':year' => $settings->commence_year]) : null, 'label' => page_text('about.stats.4')],
        ])->filter(fn ($s) => $s['value']);
    @endphp

    <x-site.page-hero :eyebrow="page_text('about.hero.eyebrow')" :title="$about?->banner_title ?? page_text('about.hero.title')" :image="img($about?->banner_picture, 1600)" :crumbs="[page_text('nav.about') => null]" />

    <section class="section">
        <div class="container-site grid gap-12 lg:grid-cols-12 lg:gap-16">
            <div class="lg:col-span-7">
                <p class="eyebrow">{{ page_text('about.story.eyebrow') }}</p>
                <h2 class="section-title">{{ $about?->history_title ?? page_text('about.story.title') }}</h2>
                <div class="prose-site mt-8 text-ink-600">{!! clean_html($about?->company_history) !!}</div>
            </div>
            <div class="lg:col-span-5">
                <div class="sticky top-28 rounded-lg bg-ink-950 p-8 text-white">
                    <x-site.icon name="steering" class="h-10 w-10 text-brand-400" />
                    <h2 class="mt-5 text-3xl text-white">{{ page_text('about.mission.title') }}</h2>
                    <div class="prose prose-invert mt-4 text-ink-200">{!! clean_html($about?->mission_statement) !!}</div>
                    @if ($stats->isNotEmpty())
                        <dl class="mt-8 grid grid-cols-2 gap-6 border-t border-white/10 pt-8">
                            @foreach ($stats as $stat)
                                <div>
                                    <dt class="text-sm text-ink-300">{{ $stat['label'] }}</dt>
                                    <dd class="order-first font-display text-4xl font-extrabold text-brand-400">{{ $stat['value'] }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @if ($founder && $founder->show_founder)
        <section class="section bg-ink-50">
            <div class="container-site grid items-center gap-12 lg:grid-cols-12">
                <div class="lg:col-span-4">
                    <img src="{{ img($founder->founder_picture, 700) }}" alt="{{ $founder->founder_name }}" class="aspect-square w-full rounded-lg object-cover" loading="lazy">
                </div>
                <figure class="lg:col-span-8">
                    <x-site.icon name="quote" class="h-10 w-10 text-brand-500" />
                    <h2 class="mt-4 text-4xl">{{ $founder->speech_title }}</h2>
                    <blockquote class="prose-site mt-6 text-ink-600">{!! clean_html($founder->speech_content) !!}</blockquote>
                    <figcaption class="mt-6 font-display text-xl font-bold uppercase">{{ $founder->founder_name }} <span class="block text-sm tracking-widest text-ink-400">{{ page_text('about.founder.caption') }}</span></figcaption>
                </figure>
            </div>
        </section>
    @endif

    @if ($achievements->isNotEmpty())
        <section class="section on-dark bg-ink-950 text-white">
            <div class="container-site">
                <div class="max-w-2xl">
                    <p class="eyebrow">{{ page_text('about.milestones.eyebrow') }}</p>
                    <h2 class="section-title text-white">{{ page_text('about.milestones.title') }}</h2>
                </div>
                <div class="relative mt-14">
                    <div class="lane-v absolute bottom-0 left-[1.1rem] top-0 opacity-60 lg:hidden" aria-hidden="true"></div>
                    <ol class="grid gap-10 lg:grid-cols-3">
                        @foreach ($achievements as $achievement)
                            <li class="relative pl-14 lg:pl-0">
                                <span class="absolute left-0 top-0 inline-flex h-9 w-9 items-center justify-center rounded-full bg-brand-400 text-ink-950 lg:static lg:mb-5"><x-site.icon name="award" class="h-5 w-5" /></span>
                                <p class="font-display text-5xl font-extrabold text-white/20">{{ $achievement->year }}</p>
                                <h3 class="mt-2 text-2xl text-white">{{ $achievement->title }}</h3>
                                <div class="mt-3 text-ink-300">{{ \Illuminate\Support\Str::limit(html_entity_decode(strip_tags($achievement->description)), 260) }}</div>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </section>
    @endif

    @if ($team->isNotEmpty())
        <section class="section">
            <div class="container-site">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-2xl">
                        <p class="eyebrow">{{ page_text('about.team.eyebrow') }}</p>
                        <h2 class="section-title">{{ page_text('about.team.title') }}</h2>
                    </div>
                    <a href="{{ route('our-instructors') }}" class="link-underline shrink-0">{{ page_text('about.team.link') }}</a>
                </div>
                <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($team as $member)
                        @include('site.partials.team-card', ['member' => $member])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <x-site.apply-cta />
</x-site.layout>
