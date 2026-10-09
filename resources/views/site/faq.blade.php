@php
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $faqs->map(fn ($faq) => [
            '@type' => 'Question',
            'name' => $faq->question,
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => strip_tags($faq->answer)],
        ])->values()->all(),
    ];
@endphp
<x-site.layout :title="page_text('faq.meta.title')" :description="page_text('faq.meta.description')" :schema="$schema">
    <x-site.page-hero :eyebrow="page_text('faq.hero.eyebrow')" :title="page_text('faq.hero.title')" :crumbs="[page_text('nav.faq') => null]"
        :lead="page_text('faq.hero.lead')" />

    <section class="section">
        <div class="container-site grid gap-12 lg:grid-cols-12">
            <div class="lg:col-span-8">
                <x-site.faq-list :faqs="$faqs" />
            </div>
            <aside class="lg:col-span-4">
                <div class="sticky top-28 rounded-lg bg-ink-950 p-8 text-white">
                    <h2 class="text-3xl text-white">{{ page_text('faq.aside.title') }}</h2>
                    <p class="mt-3 text-ink-300">{{ page_text('faq.aside.text') }}</p>
                    <div class="mt-6 space-y-3">
                        <a href="{{ route('contact') }}" class="btn-brand w-full">{{ page_text('faq.aside.button') }}</a>
                        @if ($settings?->business_contact)
                            <a href="tel:{{ preg_replace('/[^\d+]/', '', $settings->business_contact) }}" class="btn-outline-light w-full"><x-site.icon name="phone" class="h-5 w-5" /> {{ page_text('faq.aside.call') }}</a>
                        @endif
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <x-site.apply-cta />
</x-site.layout>
