@props(['label', 'value', 'icon' => null, 'hint' => null, 'href' => null, 'tone' => 'default'])
@php $tag = $href ? 'a' : 'div'; @endphp
<{{ $tag }} @if ($href) href="{{ $href }}" @endif {{ $attributes->merge(['class' => 'portal-card group flex items-start justify-between gap-4 p-5' . ($href ? ' transition hover:border-ink-950' : '')]) }}>
    <div class="min-w-0">
        <p class="text-sm font-medium text-ink-500">{{ $label }}</p>
        <p class="mt-1 font-display text-4xl font-bold leading-none text-ink-950">{{ $value }}</p>
        @if ($hint)<p @class(['mt-2 text-xs', 'text-ink-400' => $tone === 'default', 'font-semibold text-red-600' => $tone === 'alert', 'font-semibold text-green-700' => $tone === 'good'])>{{ $hint }}</p>@endif
    </div>
    @if ($icon)
        <span @class(['inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-lg', 'bg-ink-950 text-brand-400' => $tone !== 'alert', 'bg-red-100 text-red-700' => $tone === 'alert'])><x-site.icon :name="$icon" class="h-5 w-5" /></span>
    @endif
</{{ $tag }}>
