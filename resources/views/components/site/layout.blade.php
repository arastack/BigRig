@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'schema' => null,
])
@php
    $siteName = $settings->site_name ?? config('app.name');
    $pageTitle = $title ? $title . ' | ' . $siteName : $siteName . ' — Professional Driver Training in Nigeria';
    $metaDescription = \Illuminate\Support\Str::limit(strip_tags($description ?? $settings->site_description ?? ''), 160);
    $ogImage = $image ?? brand_logo('light', 1200);
    $phone = $settings->business_contact ?? null;
    $tel = $phone ? preg_replace('/[^\d+]/', '', $phone) : null;
    $whatsapp = ($settings->show_whatsapp_support ?? false) && $settings->whatsapp_support ? preg_replace('/\D/', '', $settings->whatsapp_support) : null;
    $socials = collect([
        'instagram' => $settings->instagram_handle ?? null,
        'facebook' => $settings->facebook_handle ?? null,
        'twitter' => $settings->twitter_handle ?? null,
        'youtube' => $settings->youtube_handle ?? null,
        'tiktok' => $settings->tiktok_handle ?? null,
        'linkedin' => $settings->linkedin_handle ?? null,
    ])->filter(fn ($url) => filled($url) && str_starts_with($url, 'http'));
    $nav = [
        ['label' => 'Home', 'route' => 'index'],
        ['label' => 'Courses', 'route' => 'courses', 'active' => ['courses', 'course']],
        ['label' => 'About', 'route' => 'about-us'],
        ['label' => 'Instructors', 'route' => 'our-instructors'],
        ['label' => 'FAQ', 'route' => 'faq'],
        ['label' => 'Contact', 'route' => 'contact'],
    ];
    $isActive = fn ($item) => request()->routeIs(...($item['active'] ?? [$item['route']]));
@endphp
<!DOCTYPE html>
<html lang="en-NG">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta name="theme-color" content="#0A0A09">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if ($ogImage)<meta property="og:image" content="{{ $ogImage }}">@endif
    <meta name="twitter:card" content="summary_large_image">

    @if ($settings?->favicon)
        <link rel="icon" href="{{ Storage::url($settings->favicon) }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/site.css', 'resources/js/site.js'])

    <script type="application/ld+json">
        {!! json_encode(array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'DrivingSchool',
            'name' => $siteName,
            'url' => url('/'),
            'logo' => brand_logo('light', 600),
            'telephone' => $phone,
            'email' => $settings->business_email ?? null,
            'address' => $settings?->headquarters ? ['@type' => 'PostalAddress', 'streetAddress' => $settings->headquarters, 'addressCountry' => 'NG'] : null,
            'sameAs' => $socials->values()->all() ?: null,
        ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
    </script>
    @if ($schema)
        <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
    @endif
</head>
<body class="flex min-h-screen flex-col" x-data="{ menu: false }" :class="menu && 'overflow-hidden lg:overflow-auto'">
    <a href="#main" class="sr-only z-[100] rounded bg-brand-400 px-4 py-2 font-semibold text-ink-950 focus:not-sr-only focus:fixed focus:left-4 focus:top-4">Skip to content</a>

    {{-- Utility bar --}}
    <div class="hidden bg-ink-950 text-sm text-ink-200 md:block">
        <div class="container-site flex h-10 items-center justify-between gap-6">
            <div class="flex items-center gap-6">
                @if ($phone)
                    <a href="tel:{{ $tel }}" class="inline-flex items-center gap-2 hover:text-brand-400"><x-site.icon name="phone" class="h-4 w-4 text-brand-400" />{{ $phone }}</a>
                @endif
                @if ($settings?->business_email)
                    <a href="mailto:{{ $settings->business_email }}" class="inline-flex items-center gap-2 hover:text-brand-400"><x-site.icon name="mail" class="h-4 w-4 text-brand-400" />{{ $settings->business_email }}</a>
                @endif
            </div>
            @if ($activeBatch)
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 font-medium text-white hover:text-brand-400">
                    <span class="relative flex h-2 w-2"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-brand-400 opacity-75"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-brand-400"></span></span>
                    Admissions open — {{ $activeBatch->batch_name }}
                    <x-site.icon name="arrow-right" class="h-4 w-4" />
                </a>
            @endif
        </div>
    </div>

    {{-- Header --}}
    <header class="sticky top-0 z-50 border-b border-ink-100 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/85">
        <div class="container-site flex h-20 items-center justify-between gap-6">
            <a href="{{ route('index') }}" class="flex shrink-0 items-center" aria-label="{{ $siteName }} home">
                <img src="{{ brand_logo('light', 240) }}" alt="{{ $siteName }}" class="h-14 w-auto" width="67" height="56">
            </a>

            <nav class="hidden lg:block" aria-label="Main">
                <ul class="flex items-center gap-1">
                    @foreach ($nav as $item)
                        <li>
                            <a href="{{ route($item['route']) }}" @class([
                                'relative px-3 py-2 font-display text-lg font-semibold uppercase tracking-wide transition',
                                'text-ink-950 after:absolute after:inset-x-3 after:-bottom-[21px] after:h-1 after:bg-brand-400' => $isActive($item),
                                'text-ink-500 hover:text-ink-950' => ! $isActive($item),
                            ]) @if ($isActive($item)) aria-current="page" @endif>{{ $item['label'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-outline btn-sm hidden sm:inline-flex">My portal</a>
                @else
                    <a href="{{ route('login') }}" class="hidden px-3 py-2 font-display text-lg font-semibold uppercase tracking-wide text-ink-500 hover:text-ink-950 sm:inline-flex">Sign in</a>
                    <a href="{{ route('register') }}" class="btn-brand btn-sm">Apply now</a>
                @endauth
                <button type="button" class="-mr-2 inline-flex h-11 w-11 items-center justify-center rounded-md text-ink-950 hover:bg-ink-50 lg:hidden" @click="menu = true" aria-label="Open menu" :aria-expanded="menu.toString()" aria-controls="mobile-menu">
                    <x-site.icon name="menu" class="h-6 w-6" />
                </button>
            </div>
        </div>
    </header>

    {{-- Mobile menu --}}
    <div id="mobile-menu" x-show="menu" x-cloak class="fixed inset-0 z-[60] lg:hidden" role="dialog" aria-modal="true" aria-label="Menu" @keydown.escape.window="menu = false">
        <div x-show="menu" x-transition.opacity class="absolute inset-0 bg-ink-950/60" @click="menu = false"></div>
        <div x-show="menu" x-trap.noscroll="menu" x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
             class="absolute inset-y-0 right-0 flex w-full max-w-sm flex-col bg-ink-950 text-white">
            <div class="flex h-20 items-center justify-between px-5">
                <img src="{{ brand_logo('dark', 240) }}" alt="{{ $siteName }}" class="h-12 w-auto">
                <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-md hover:bg-white/10" @click="menu = false" aria-label="Close menu"><x-site.icon name="x" class="h-6 w-6" /></button>
            </div>
            <div class="lane mx-5 opacity-80"></div>
            <nav class="flex-1 overflow-y-auto px-5 py-6" aria-label="Mobile">
                <ul class="space-y-1">
                    @foreach ($nav as $item)
                        <li>
                            <a href="{{ route($item['route']) }}" @class(['flex items-center justify-between rounded-md px-3 py-3 font-display text-2xl font-bold uppercase tracking-wide', 'bg-white/10 text-brand-400' => $isActive($item), 'hover:bg-white/5' => ! $isActive($item)])>
                                {{ $item['label'] }} <x-site.icon name="arrow-right" class="h-5 w-5 opacity-50" />
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
            <div class="space-y-3 border-t border-white/10 p-5">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-brand w-full">My portal</a>
                @else
                    <a href="{{ route('register') }}" class="btn-brand w-full">Apply now</a>
                    <a href="{{ route('login') }}" class="btn-outline-light w-full">Sign in</a>
                @endauth
                @if ($phone)
                    <a href="tel:{{ $tel }}" class="flex items-center justify-center gap-2 pt-2 text-sm text-ink-300"><x-site.icon name="phone" class="h-4 w-4 text-brand-400" />{{ $phone }}</a>
                @endif
            </div>
        </div>
    </div>

    @if (session('success') || session('error') || session('info'))
        <div class="container-site pt-6" role="status">
            <div @class([
                'flex items-start gap-3 rounded-lg border px-4 py-3 text-sm font-medium',
                'border-green-200 bg-green-50 text-green-900' => session('success'),
                'border-red-200 bg-red-50 text-red-900' => session('error'),
                'border-ink-200 bg-ink-50 text-ink-900' => session('info'),
            ])>
                <x-site.icon :name="session('error') ? 'x' : 'check-circle'" class="mt-0.5 h-5 w-5 shrink-0" />
                <p>{{ session('success') ?? session('error') ?? session('info') }}</p>
            </div>
        </div>
    @endif

    <main id="main" class="flex-1">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="on-dark relative bg-ink-950 text-ink-300">
        <div class="hazard h-2" aria-hidden="true"></div>

        <div id="newsletter" class="border-b border-white/10">
            <div class="container-site grid gap-6 py-12 lg:grid-cols-2 lg:items-center">
                <div>
                    <h2 class="text-3xl text-white sm:text-4xl">Get intake dates first</h2>
                    <p class="mt-2 text-ink-300">News on new batches, schedules and driving careers. No spam — unsubscribe any time.</p>
                </div>
                <form method="POST" action="{{ route('email-subscription') }}" class="w-full">
                    @csrf
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <label for="newsletter-email" class="sr-only">Email address</label>
                        <input id="newsletter-email" type="email" name="email" required autocomplete="email" placeholder="you@example.com" value="{{ old('email') }}"
                               class="h-12 w-full rounded-md border-0 bg-white/10 px-4 text-white ring-1 ring-white/20 placeholder:text-ink-400 focus:bg-white/15 focus:ring-2 focus:ring-brand-400">
                        <button class="btn-brand h-12 shrink-0">Subscribe</button>
                    </div>
                    @error('email', 'newsletter')<p class="mt-2 text-sm text-red-300">{{ $message }}</p>@enderror
                    @if (session('newsletter'))<p class="mt-2 text-sm text-brand-300" role="status">{{ session('newsletter') }}</p>@endif
                </form>
            </div>
        </div>

        <div class="container-site grid gap-10 py-14 sm:grid-cols-2 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <img src="{{ brand_logo('dark', 320) }}" alt="{{ $siteName }}" class="h-20 w-auto" loading="lazy">
                <p class="mt-5 max-w-sm text-sm leading-relaxed">{{ \Illuminate\Support\Str::limit(strip_tags($settings->site_description ?? ''), 180) }}</p>
                @if ($socials->isNotEmpty())
                    <ul class="mt-6 flex gap-2">
                        @foreach ($socials as $network => $url)
                            <li><a href="{{ $url }}" target="_blank" rel="noopener" class="inline-flex h-10 w-10 items-center justify-center rounded-md bg-white/5 text-white ring-1 ring-white/10 hover:bg-brand-400 hover:text-ink-950" aria-label="{{ ucfirst($network) }}"><x-site.icon :name="$network" class="h-5 w-5" /></a></li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="lg:col-span-2">
                <h3 class="text-lg text-white">Explore</h3>
                <ul class="mt-4 space-y-2.5 text-sm">
                    @foreach ($nav as $item)
                        <li><a href="{{ route($item['route']) }}" class="hover:text-brand-400">{{ $item['label'] }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div class="lg:col-span-3">
                <h3 class="text-lg text-white">Admissions</h3>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="{{ route('register') }}" class="hover:text-brand-400">Apply online</a></li>
                    <li><a href="{{ route('register.form-pdf') }}" class="hover:text-brand-400">Download application form (PDF)</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-brand-400">Track my application</a></li>
                    <li><a href="{{ route('faq') }}" class="hover:text-brand-400">Admission questions</a></li>
                </ul>
            </div>

            <div class="lg:col-span-3">
                <h3 class="text-lg text-white">Visit us</h3>
                <ul class="mt-4 space-y-3 text-sm">
                    @if ($settings?->headquarters)
                        <li class="flex gap-3"><x-site.icon name="map-pin" class="mt-0.5 h-5 w-5 shrink-0 text-brand-400" /><span>{{ $settings->headquarters }}</span></li>
                    @endif
                    @if ($phone)
                        <li><a href="tel:{{ $tel }}" class="flex gap-3 hover:text-brand-400"><x-site.icon name="phone" class="mt-0.5 h-5 w-5 shrink-0 text-brand-400" />{{ $phone }}</a></li>
                    @endif
                    @if ($settings?->business_email)
                        <li><a href="mailto:{{ $settings->business_email }}" class="flex gap-3 break-all hover:text-brand-400"><x-site.icon name="mail" class="mt-0.5 h-5 w-5 shrink-0 text-brand-400" />{{ $settings->business_email }}</a></li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="border-t border-white/10">
            <div class="container-site flex flex-col gap-3 py-6 text-xs text-ink-400 sm:flex-row sm:items-center sm:justify-between">
                <p>&copy; {{ now()->year }} {{ $siteName }}. All rights reserved.</p>
                <ul class="flex gap-5">
                    <li><a href="{{ route('privacy') }}" class="hover:text-white">Privacy policy</a></li>
                    <li><a href="{{ route('terms') }}" class="hover:text-white">Terms &amp; conditions</a></li>
                </ul>
            </div>
        </div>
    </footer>

    @if ($whatsapp)
        <a href="https://wa.me/{{ $whatsapp }}?text={{ rawurlencode('Hello BigRig, I would like to know more about your courses.') }}" target="_blank" rel="noopener"
           class="fixed bottom-5 right-5 z-40 inline-flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366] text-white shadow-lg shadow-ink-950/20 ring-4 ring-white transition hover:scale-105"
           aria-label="Chat with us on WhatsApp">
            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.5 14.4c-.3-.1-1.8-.9-2-1-.3-.1-.5-.1-.7.1-.2.3-.8 1-.9 1.2-.2.2-.3.2-.6.1-.3-.1-1.3-.5-2.4-1.5-.9-.8-1.5-1.8-1.7-2.1-.2-.3 0-.5.1-.6l.4-.5.3-.5c.1-.2 0-.4 0-.5l-.9-2.2c-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.5s1.1 2.9 1.2 3.1c.1.2 2.1 3.2 5.1 4.5.7.3 1.3.5 1.7.6.7.2 1.4.2 1.9.1.6-.1 1.8-.7 2-1.4.2-.7.2-1.3.2-1.4-.1-.1-.3-.2-.6-.3zM12 21.8h-.1a9.9 9.9 0 0 1-5-1.4l-.4-.2-3.7 1 1-3.6-.2-.4a9.8 9.8 0 0 1-1.5-5.2C2.1 6.6 6.5 2.2 12 2.2c2.6 0 5.1 1 7 2.9a9.8 9.8 0 0 1 2.9 7c0 5.4-4.4 9.8-9.9 9.8z"/></svg>
        </a>
    @endif
</body>
</html>
