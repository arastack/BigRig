@php
    $user = Auth::user();
    $navigation = \App\Support\PortalNavigation::for($user);
    $portalName = ['admin' => 'Administration', 'instructor' => 'Instructor portal', 'student' => 'Student portal'][$user->portal()] ?? 'Portal';
    $roleName = $user->roles->first()?->role_name;
    // Sections are already escaped by Blade; decode so {{ }} doesn't escape them twice
    $pageTitle = html_entity_decode(trim($__env->yieldContent('title')), ENT_QUOTES) ?: ($attributes['title'] ?? $portalName);

    $isActive = function (array $item) {
        $patterns = $item['active'] ?? [$item['route']];
        if (! request()->routeIs(...$patterns)) {
            return false;
        }
        // Items sharing a route (e.g. Applicants / Students) are told apart by query string
        foreach ($item['query'] ?? [] as $key => $value) {
            if (request($key, 'awaiting_payment') !== $value) {
                return false;
            }
        }

        return true;
    };
@endphp
<!DOCTYPE html>
<html lang="en-NG">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex">
    <title>{{ $pageTitle }} | {{ $settings->site_name ?? config('app.name') }}</title>
    @if ($settings?->favicon)<link rel="icon" href="{{ Storage::url($settings->favicon) }}">@endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <style>.ck-editor__editable { min-height: 10rem; } .ck.ck-editor { color: #1A1A17; }</style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('head')
</head>
<body class="bg-ink-50 font-inter text-ink-700 antialiased" x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false">
    <a href="#portal-main" class="sr-only z-[100] rounded bg-brand-400 px-4 py-2 font-semibold text-ink-950 focus:not-sr-only focus:fixed focus:left-4 focus:top-4">Skip to content</a>

    {{-- Mobile backdrop --}}
    <div class="fixed inset-0 z-40 bg-ink-950/60 lg:hidden" x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false" x-cloak></div>

    {{-- Sidebar --}}
    <aside id="portal-sidebar" class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col bg-ink-950 transition-transform duration-200 lg:translate-x-0"
           :class="sidebarOpen && 'translate-x-0'" aria-label="Main navigation">
        <div class="flex h-20 shrink-0 items-center justify-between px-5">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <img src="{{ brand_logo('dark', 200) }}" alt="{{ $settings->site_name ?? 'BigRig' }}" class="h-12 w-auto">
                <span class="font-display text-sm font-semibold uppercase leading-tight tracking-widest text-ink-300">{{ $portalName }}</span>
            </a>
            <button type="button" class="rounded-md p-2 text-ink-300 hover:bg-white/10 lg:hidden" @click="sidebarOpen = false" aria-label="Close menu"><x-site.icon name="x" class="h-5 w-5" /></button>
        </div>
        <div class="lane mx-5 opacity-60" aria-hidden="true"></div>

        <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-6">
            @foreach ($navigation as $section)
                <div>
                    @if ($section['title'])
                        <p class="mb-2 px-3 font-display text-xs font-semibold uppercase tracking-[0.2em] text-ink-400">{{ $section['title'] }}</p>
                    @endif
                    <ul class="space-y-0.5">
                        @foreach ($section['items'] as $item)
                            @php $active = $isActive($item); @endphp
                            <li>
                                <a href="{{ route($item['route'], $item['params'] ?? []) }}" @class(['nav-link', 'nav-link-active' => $active]) @if ($active) aria-current="page" @endif>
                                    <x-site.icon :name="$item['icon']" class="h-[18px] w-[18px] shrink-0" />
                                    <span class="flex-1">{{ $item['label'] }}</span>
                                    @if (! empty($item['badge']))
                                        <span @class(['rounded-full px-2 py-0.5 text-xs font-bold', 'bg-ink-950 text-brand-400' => $active, 'bg-brand-400 text-ink-950' => ! $active])>{{ $item['badge'] }}</span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </nav>

        <div class="border-t border-white/10 p-3">
            <a href="{{ route('index') }}" class="nav-link" target="_blank"><x-site.icon name="external" class="h-[18px] w-[18px]" /> View website</a>
        </div>
    </aside>

    <div class="lg:pl-72">
        {{-- Top bar --}}
        <header class="sticky top-0 z-30 border-b border-ink-100 bg-white/90 backdrop-blur">
            <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                <div class="flex min-w-0 items-center gap-3">
                    <button type="button" class="-ml-2 rounded-md p-2 text-ink-600 hover:bg-ink-50 lg:hidden" @click="sidebarOpen = true" aria-controls="portal-sidebar" :aria-expanded="sidebarOpen.toString()" aria-label="Open menu">
                        <x-site.icon name="menu" class="h-6 w-6" />
                    </button>
                    <p class="truncate font-display text-lg font-semibold uppercase tracking-wide text-ink-950">{{ $pageTitle }}</p>
                </div>

                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button type="button" class="flex items-center gap-3 rounded-lg p-1.5 pr-2 hover:bg-ink-50" @click="open = !open" :aria-expanded="open.toString()" aria-haspopup="menu">
                        <x-portal.avatar :user="$user" size="h-9 w-9" class="ring-2 ring-brand-400" />
                        <span class="hidden text-left sm:block">
                            <span class="block text-sm font-semibold leading-tight text-ink-950">{{ $user->firstName }} {{ $user->lastName }}</span>
                            <span class="block text-xs text-ink-500">{{ $roleName }}</span>
                        </span>
                        <x-site.icon name="chevron-down" class="hidden h-4 w-4 text-ink-400 sm:block" />
                    </button>
                    <div x-show="open" x-cloak x-transition.origin.top.right class="absolute right-0 mt-2 w-56 overflow-hidden rounded-xl border border-ink-100 bg-white py-1 shadow-lg" role="menu">
                        <div class="border-b border-ink-100 px-4 py-3">
                            <p class="truncate text-sm font-semibold text-ink-950">{{ $user->full_name }}</p>
                            <p class="truncate text-xs text-ink-500">{{ $user->email }}</p>
                        </div>
                        <a href="{{ route('profile.show') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-ink-700 hover:bg-ink-50" role="menuitem"><x-site.icon name="user" class="h-4 w-4" /> Profile &amp; security</a>
                        @if ($user->isStudent())
                            <a href="{{ route('application') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-ink-700 hover:bg-ink-50" role="menuitem"><x-site.icon name="file-text" class="h-4 w-4" /> My application</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50" role="menuitem"><x-site.icon name="log-out" class="h-4 w-4" /> Sign out</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Flash messages --}}
        <div class="pointer-events-none fixed bottom-4 right-4 z-[60] flex w-full max-w-sm flex-col gap-2 px-4 sm:px-0" aria-live="polite">
            @foreach (['success' => ['check-circle', 'border-green-200 bg-white text-green-900', 'text-green-600'], 'info' => ['check-circle', 'border-ink-200 bg-white text-ink-900', 'text-ink-600'], 'warning' => ['alert', 'border-brand-500 bg-brand-50 text-ink-900', 'text-brand-700'], 'error' => ['alert', 'border-red-200 bg-white text-red-900', 'text-red-600']] as $key => [$icon, $style, $iconStyle])
                @if (session($key))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, {{ $key === 'error' ? 9000 : 6000 }})" x-transition
                         class="pointer-events-auto flex items-start gap-3 rounded-xl border px-4 py-3 text-sm shadow-lg {{ $style }}" role="{{ $key === 'error' ? 'alert' : 'status' }}">
                        <x-site.icon :name="$icon" class="mt-0.5 h-5 w-5 shrink-0 {{ $iconStyle }}" />
                        <p class="flex-1">{{ session($key) }}</p>
                        <button type="button" @click="show = false" class="opacity-50 hover:opacity-100" aria-label="Dismiss"><x-site.icon name="x" class="h-4 w-4" /></button>
                    </div>
                @endif
            @endforeach
            @if ($errors->any())
                <div x-data="{ show: true }" x-show="show" class="pointer-events-auto rounded-xl border border-red-200 bg-white px-4 py-3 text-sm text-red-900 shadow-lg" role="alert">
                    <div class="flex items-start justify-between gap-3">
                        <p class="font-semibold">Please fix the following:</p>
                        <button type="button" @click="show = false" class="opacity-50 hover:opacity-100" aria-label="Dismiss"><x-site.icon name="x" class="h-4 w-4" /></button>
                    </div>
                    <ul class="mt-1 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
        </div>

        <main id="portal-main" class="min-h-[calc(100vh-4rem)]">
            {{ $slot }}
        </main>
    </div>

    @livewireScriptConfig
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            if (!Alpine.store('deleteModal')) {
                Alpine.store('deleteModal', {
                    modalOpen: false,
                    sliderId: null,
                    open(id) { this.sliderId = id; this.modalOpen = true },
                    close() { this.modalOpen = false; this.sliderId = null },
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
