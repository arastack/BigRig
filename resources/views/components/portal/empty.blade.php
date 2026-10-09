@props(['icon' => 'inbox', 'title', 'text' => null])
<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center px-6 py-14 text-center']) }}>
    <span class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-ink-50 text-ink-400"><x-site.icon :name="$icon" class="h-7 w-7" /></span>
    <p class="mt-4 font-semibold text-ink-900">{{ $title }}</p>
    @if ($text)<p class="mt-1 max-w-sm text-sm text-ink-500">{{ $text }}</p>@endif
    {{ $slot }}
</div>
