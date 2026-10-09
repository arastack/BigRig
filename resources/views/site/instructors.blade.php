<x-site.layout title="Our instructors" description="Meet the certified driving instructors and team who train BigRig students in truck, forklift, bus and car driving.">
    <x-site.page-hero eyebrow="Our team" title="Instructors who have driven the miles" :crumbs="['Instructors' => null]"
        lead="Every BigRig student is assigned a dedicated instructor for each course — experienced professionals who teach safe, confident and defensive driving." />

    <section class="section">
        <div class="container-site">
            @if ($team->isNotEmpty())
                <div class="grid gap-x-6 gap-y-12 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($team as $member)
                        @include('site.partials.team-card', ['member' => $member])
                    @endforeach
                </div>
            @else
                <p class="lead">Our instructor profiles are coming soon.</p>
            @endif
        </div>
    </section>

    <section class="bg-ink-50 py-16">
        <div class="container-site grid gap-8 md:grid-cols-3">
            @foreach ([
                ['shield', 'Safety first', 'Defensive driving and road regulations are part of every programme, not an optional extra.'],
                ['users', 'Small groups', 'Practical sessions are scheduled so you get real time behind the wheel with your instructor.'],
                ['award', 'Exam ready', 'Mock tests and inspection practice prepare you for certification and your licence test.'],
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
