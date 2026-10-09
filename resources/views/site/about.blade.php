<x-site.layout title="About us" :description="\Illuminate\Support\Str::limit(strip_tags($about?->mission_statement ?? ''), 160)">
    @php
        $stats = collect([
            ['value' => $about?->students_count ? number_format($about->students_count) . '+' : null, 'label' => 'Drivers trained'],
            ['value' => $about?->pass_rate ? rtrim(rtrim(number_format($about->pass_rate, 1), '0'), '.') . '%' : null, 'label' => 'First-time pass rate'],
            ['value' => $about?->instructors_count ?: null, 'label' => 'Certified instructors'],
            ['value' => $settings?->commence_year ? 'Since ' . $settings->commence_year : null, 'label' => 'Training drivers in Ibadan'],
        ])->filter(fn ($s) => $s['value']);
    @endphp

    <x-site.page-hero eyebrow="About BigRig" :title="$about?->banner_title ?? 'About our driving school'" :image="img($about?->banner_picture, 1600)" :crumbs="['About' => null]" />

    <section class="section">
        <div class="container-site grid gap-12 lg:grid-cols-12 lg:gap-16">
            <div class="lg:col-span-7">
                <p class="eyebrow">Our story</p>
                <h2 class="section-title">{{ $about?->history_title ?? 'Driving excellence' }}</h2>
                <div class="prose-site mt-8 text-ink-600">{!! clean_html($about?->company_history) !!}</div>
            </div>
            <div class="lg:col-span-5">
                <div class="sticky top-28 rounded-lg bg-ink-950 p-8 text-white">
                    <x-site.icon name="steering" class="h-10 w-10 text-brand-400" />
                    <h2 class="mt-5 text-3xl text-white">Our mission</h2>
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
                    <figcaption class="mt-6 font-display text-xl font-bold uppercase">{{ $founder->founder_name }} <span class="block text-sm tracking-widest text-ink-400">Founder</span></figcaption>
                </figure>
            </div>
        </section>
    @endif

    @if ($achievements->isNotEmpty())
        <section class="section on-dark bg-ink-950 text-white">
            <div class="container-site">
                <div class="max-w-2xl">
                    <p class="eyebrow">Milestones</p>
                    <h2 class="section-title text-white">Recognition along the way</h2>
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
                        <p class="eyebrow">The people behind the wheel</p>
                        <h2 class="section-title">Meet the team</h2>
                    </div>
                    <a href="{{ route('our-instructors') }}" class="link-underline shrink-0">All instructors</a>
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
