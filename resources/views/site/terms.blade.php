@php
    $name = $settings->site_name ?? 'BigRig International Truck Driving School';
    $email = $settings->business_email ?? null;
    $saved = \App\Models\PageText::where('key', 'terms.body')->value('updated_at');
    $updated = $saved ? \Carbon\Carbon::parse($saved) : \Carbon\Carbon::parse('2026-10-09');
    $body = strtr(clean_html(page_text('terms.body')), [
        '{site_name}' => e($name),
        '{email}' => $email ? '<a href="mailto:' . e($email) . '">' . e($email) . '</a>' : 'our admissions office',
        '{address}' => e($settings->headquarters ?? 'our office'),
    ]);
@endphp
<x-site.layout :title="page_text('terms.meta.title')" :description="page_text('terms.meta.description')">
    <x-site.page-hero :eyebrow="page_text('terms.hero.eyebrow')" :title="page_text('terms.title')" :crumbs="[page_text('footer.terms') => null]" :lead="page_text('terms.updated', [':date' => $updated->format('j F Y')])" />

    <section class="section">
        <div class="container-site">
            <div class="prose-site mx-auto max-w-3xl text-ink-600">
                {!! $body !!}
            </div>
        </div>
    </section>
</x-site.layout>
