@props(['eyebrow' => null, 'title', 'lead' => null, 'image' => null, 'crumbs' => []])
{{-- Header band for inner pages --}}
<section class="on-dark relative isolate overflow-hidden bg-ink-950 text-white">
    @if ($image)
        <img src="{{ $image }}" alt="" class="absolute inset-0 -z-10 h-full w-full object-cover opacity-30" fetchpriority="high">
        <div class="absolute inset-0 -z-10 bg-gradient-to-r from-ink-950 via-ink-950/90 to-ink-950/40"></div>
    @endif
    <div class="container-site relative py-16 sm:py-20 lg:py-24">
        @if ($crumbs)
            <nav aria-label="Breadcrumb" class="mb-6">
                <ol class="flex flex-wrap items-center gap-2 text-sm text-ink-300">
                    <li><a href="{{ route('index') }}" class="hover:text-brand-400">{{ page_text('nav.home') }}</a></li>
                    @foreach ($crumbs as $label => $url)
                        <li aria-hidden="true">/</li>
                        <li>@if ($url)<a href="{{ $url }}" class="hover:text-brand-400">{{ $label }}</a>@else<span class="text-white" aria-current="page">{{ $label }}</span>@endif</li>
                    @endforeach
                </ol>
            </nav>
        @endif
        @if ($eyebrow)<p class="eyebrow">{{ $eyebrow }}</p>@endif
        <h1 class="mt-3 max-w-4xl text-5xl text-white sm:text-6xl lg:text-7xl">{{ $title }}</h1>
        @if ($lead)<p class="mt-5 max-w-2xl text-lg leading-relaxed text-ink-200">{{ $lead }}</p>@endif
        {{ $slot }}
    </div>
    <div class="lane absolute inset-x-0 bottom-6 opacity-70" aria-hidden="true"></div>
</section>
