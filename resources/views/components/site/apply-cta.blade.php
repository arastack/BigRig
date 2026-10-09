@props(['title' => null, 'lead' => null])
<section class="relative overflow-hidden bg-brand-400">
    <div class="pointer-events-none absolute -right-10 top-1/2 hidden -translate-y-1/2 select-none font-display text-[16rem] font-extrabold uppercase leading-none text-ink-950/[0.06] lg:block" aria-hidden="true">BigRig</div>
    <div class="container-site relative flex flex-col gap-8 py-14 lg:flex-row lg:items-center lg:justify-between lg:py-16">
        <div class="max-w-2xl">
            <h2 class="text-4xl sm:text-5xl">{{ $title ?? page_text('cta.title') }}</h2>
            <p class="mt-3 text-lg text-ink-800">{{ $lead ?? page_text('cta.lead') }}</p>
        </div>
        <div class="flex flex-col gap-3 sm:flex-row lg:shrink-0">
            <a href="{{ route('register') }}" class="btn-dark">{{ page_text('cta.button_primary') }} <x-site.icon name="arrow-right" class="h-5 w-5" /></a>
            <a href="{{ route('register.form-pdf') }}" class="btn-outline"><x-site.icon name="download" class="h-5 w-5" /> {{ page_text('cta.button_secondary') }}</a>
        </div>
    </div>
</section>
