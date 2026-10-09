@props(['title', 'subtitle' => null, 'back' => null])
{{-- Standard page frame: heading, optional back link and actions, then content --}}
<div {{ $attributes->merge(['class' => 'mx-auto w-full max-w-[96rem] px-4 py-8 sm:px-6 lg:px-8']) }}>
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div class="min-w-0">
            @if ($back)
                <a href="{{ $back }}" class="mb-2 inline-flex items-center gap-1 text-sm font-medium text-ink-500 hover:text-ink-950"><x-site.icon name="chevron-left" class="h-4 w-4" /> Back</a>
            @endif
            <h1 class="font-display text-4xl font-bold uppercase leading-none tracking-tight text-ink-950 sm:text-5xl">{{ $title }}</h1>
            @if ($subtitle)<p class="mt-2 text-ink-500">{{ $subtitle }}</p>@endif
        </div>
        @isset($actions)
            <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
        @endisset
    </div>
    {{ $slot }}
</div>
