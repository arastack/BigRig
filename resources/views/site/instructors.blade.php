<x-site.layout :title="page_text('instructors.meta.title')" :description="page_text('instructors.meta.description')">
    <x-site.page-hero :eyebrow="page_text('instructors.hero.eyebrow')" :title="page_text('instructors.hero.title')" :crumbs="['Instructors' => null]"
        :lead="page_text('instructors.hero.lead')" />

    <section class="section">
        <div class="container-site">
            @if ($team->isNotEmpty())
                <div class="grid gap-x-6 gap-y-12 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($team as $member)
                        @include('site.partials.team-card', ['member' => $member])
                    @endforeach
                </div>
            @else
                <p class="lead">{{ page_text('instructors.empty') }}</p>
            @endif
        </div>
    </section>

    <section class="bg-ink-50 py-16">
        <div class="container-site grid gap-8 md:grid-cols-3">
            @foreach ([
                ['shield', page_text('instructors.values.1.title'), page_text('instructors.values.1.text')],
                ['users', page_text('instructors.values.2.title'), page_text('instructors.values.2.text')],
                ['award', page_text('instructors.values.3.title'), page_text('instructors.values.3.text')],
            ] as [$icon, $title, $text])
                <div>
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-md bg-ink-950 text-brand-400"><x-site.icon :name="$icon" class="h-6 w-6" /></span>
                    <h2 class="mt-5 text-3xl">{{ $title }}</h2>
                    <p class="mt-2 text-ink-500">{{ $text }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <x-site.apply-cta />
</x-site.layout>
