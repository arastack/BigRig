@props(['faqs'])
<div {{ $attributes->merge(['class' => 'divide-y divide-ink-100 border-y border-ink-100']) }} x-data="{ open: null }">
    @foreach ($faqs as $faq)
        <div>
            <h3 class="text-xl normal-case tracking-normal">
                <button type="button" class="flex w-full items-start justify-between gap-6 py-5 text-left font-inter text-lg font-semibold text-ink-950" @click="open = open === {{ $faq->id }} ? null : {{ $faq->id }}" :aria-expanded="(open === {{ $faq->id }}).toString()" aria-controls="faq-{{ $faq->id }}">
                    <span>{{ $faq->question }}</span>
                    <span class="mt-0.5 inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-ink-50 transition" :class="open === {{ $faq->id }} && 'rotate-180 bg-brand-400'"><x-site.icon name="chevron-down" class="h-4 w-4" /></span>
                </button>
            </h3>
            <div id="faq-{{ $faq->id }}" x-show="open === {{ $faq->id }}" x-collapse x-cloak>
                <div class="prose-site prose-base pb-6 pr-12 text-ink-600">{!! clean_html($faq->answer) !!}</div>
            </div>
        </div>
    @endforeach
</div>
