@props(['title', 'wide' => false])
@php $siteName = $settings->site_name ?? config('app.name'); @endphp
<!DOCTYPE html>
<html lang="en-NG">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $title }} | {{ $siteName }}</title>
    @if ($settings?->favicon)<link rel="icon" href="{{ Storage::url($settings->favicon) }}">@endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/site.css', 'resources/js/site.js'])
</head>
<body class="min-h-screen bg-ink-50">
    @if ($wide)
        <header class="border-b border-ink-100 bg-white">
            <div class="container-site flex h-20 items-center justify-between">
                <a href="{{ route('index') }}" aria-label="{{ $siteName }} home"><img src="{{ brand_logo('light', 240) }}" alt="{{ $siteName }}" class="h-14 w-auto"></a>
                <div class="text-sm text-ink-500">
                    @auth
                        <form method="POST" action="{{ route('logout') }}">@csrf<button class="font-semibold text-ink-950 underline decoration-brand-400 decoration-2 underline-offset-4">Sign out</button></form>
                    @else
                        Already applied? <a href="{{ route('login') }}" class="font-semibold text-ink-950 underline decoration-brand-400 decoration-2 underline-offset-4">Sign in</a>
                    @endauth
                </div>
            </div>
        </header>
        <main class="pb-20">{{ $slot }}</main>
    @else
        <div class="grid min-h-screen lg:grid-cols-2">
            <main class="flex flex-col bg-white">
                <div class="flex h-20 items-center px-6 sm:px-10">
                    <a href="{{ route('index') }}" aria-label="{{ $siteName }} home"><img src="{{ brand_logo('light', 240) }}" alt="{{ $siteName }}" class="h-14 w-auto"></a>
                </div>
                <div class="flex flex-1 items-center px-6 py-10 sm:px-10">
                    <div class="mx-auto w-full max-w-md">{{ $slot }}</div>
                </div>
                <p class="px-6 pb-6 text-xs text-ink-400 sm:px-10">&copy; {{ now()->year }} {{ $siteName }} · <a href="{{ route('privacy') }}" class="hover:text-ink-700">Privacy</a> · <a href="{{ route('terms') }}" class="hover:text-ink-700">Terms</a></p>
            </main>
            <aside class="on-dark relative hidden overflow-hidden bg-ink-950 text-white lg:flex lg:flex-col lg:justify-end">
                <div class="pointer-events-none absolute -right-24 top-0 h-full w-1/2 skew-x-[-12deg] bg-brand-400" aria-hidden="true"></div>
                @php $hero = \App\Models\Slider::whereNotNull('image_url')->orderBy('id')->value('image_url'); @endphp
                @if ($hero)
                    <img src="{{ img($hero, 1000) }}" alt="" class="absolute right-0 top-1/2 w-[85%] -translate-y-[60%] object-contain drop-shadow-[0_30px_40px_rgba(0,0,0,0.5)]">
                @endif
                <div class="relative z-10 p-12">
                    <p class="eyebrow">{{ page_text('auth.eyebrow') }}</p>
                    <p class="mt-4 max-w-md font-display text-5xl font-bold uppercase leading-[0.95] text-white">{{ page_text('auth.title') }}</p>
                    <ul class="mt-8 space-y-3 text-ink-200">
                        <li class="flex items-center gap-3"><x-site.icon name="check-circle" class="h-5 w-5 text-brand-400" />{{ page_text('auth.points.1') }}</li>
                        <li class="flex items-center gap-3"><x-site.icon name="check-circle" class="h-5 w-5 text-brand-400" />{{ page_text('auth.points.2') }}</li>
                        <li class="flex items-center gap-3"><x-site.icon name="check-circle" class="h-5 w-5 text-brand-400" />{{ page_text('auth.points.3') }}</li>
                    </ul>
                </div>
                <div class="lane relative z-10 mb-10"></div>
            </aside>
        </div>
    @endif
</body>
</html>
