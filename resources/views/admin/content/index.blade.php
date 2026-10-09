@section('title', 'Website content')
<x-app-layout>
    <x-portal.page title="Website content" subtitle="Everything visitors see on the public website. Pick a section to edit it.">
        <x-slot:actions>
            <a href="{{ route('index') }}" target="_blank" rel="noopener" class="btn-ghost btn-sm"><x-site.icon name="external" class="h-4 w-4" /> Open website</a>
        </x-slot:actions>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($sections as $section)
                <article class="portal-card group relative flex flex-col p-5 transition hover:border-ink-950">
                    <div class="flex items-start justify-between gap-4">
                        <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-ink-950 text-brand-400"><x-site.icon :name="$section['icon']" class="h-5 w-5" /></span>
                        <span @class(['badge-yellow' => $section['count'] === 'Not set up', 'badge-grey' => $section['count'] !== 'Not set up'])>{{ $section['count'] }}</span>
                    </div>
                    <h2 class="mt-4 font-display text-2xl font-bold uppercase leading-tight text-ink-950">
                        <a href="{{ route($section['route']) }}" class="after:absolute after:inset-0 after:rounded-xl focus:outline-none">{{ $section['title'] }}</a>
                    </h2>
                    <p class="mt-1 flex-1 text-sm text-ink-500">{{ $section['text'] }}</p>
                    <div class="mt-4 flex items-center justify-between gap-3 border-t border-ink-100 pt-4 text-sm">
                        <span class="inline-flex items-center gap-1 font-semibold text-ink-950">Edit <x-site.icon name="arrow-right" class="h-4 w-4 transition group-hover:translate-x-0.5" /></span>
                        @if (! empty($section['site']))
                            <a href="{{ $section['site'] }}" target="_blank" rel="noopener" class="relative z-10 inline-flex items-center gap-1 text-ink-500 hover:text-ink-950">View on site <x-site.icon name="external" class="h-3.5 w-3.5" /></a>
                        @endif
                    </div>
                </article>
            @endforeach

            @php $editedTexts = \App\Models\PageText::count(); @endphp
            <article class="portal-card group relative flex flex-col p-5 transition hover:border-ink-950">
                <div class="flex items-start justify-between gap-4">
                    <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-ink-950 text-brand-400"><x-site.icon name="file-text" class="h-5 w-5" /></span>
                    <span class="badge-grey">{{ $editedTexts ? number_format($editedTexts) . ' ' . str('block')->plural($editedTexts) . ' edited' : 'Using defaults' }}</span>
                </div>
                <h2 class="mt-4 font-display text-2xl font-bold uppercase leading-tight text-ink-950">
                    <a href="{{ route('admin.page-text') }}" class="after:absolute after:inset-0 after:rounded-xl focus:outline-none">Page text</a>
                </h2>
                <p class="mt-1 flex-1 text-sm text-ink-500">Headings, introductions, buttons, the footer, sign-in panel and the privacy and terms pages.</p>
                <div class="mt-4 flex items-center justify-between gap-3 border-t border-ink-100 pt-4 text-sm">
                    <span class="inline-flex items-center gap-1 font-semibold text-ink-950">Edit <x-site.icon name="arrow-right" class="h-4 w-4 transition group-hover:translate-x-0.5" /></span>
                    <a href="{{ route('index') }}" target="_blank" rel="noopener" class="relative z-10 inline-flex items-center gap-1 text-ink-500 hover:text-ink-950">View on site <x-site.icon name="external" class="h-3.5 w-3.5" /></a>
                </div>
            </article>
        </div>
    </x-portal.page>
</x-app-layout>
