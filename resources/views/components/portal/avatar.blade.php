@props(['user', 'size' => 'h-10 w-10'])
@if ($user?->profile_photo_path)
    <img src="{{ img($user->profile_photo_path, 120) }}" alt="" {{ $attributes->merge(['class' => "$size shrink-0 rounded-full object-cover"]) }}>
@else
    <span {{ $attributes->merge(['class' => "$size inline-flex shrink-0 items-center justify-center rounded-full bg-ink-950 text-xs font-bold text-brand-400"]) }} aria-hidden="true">{{ mb_substr($user?->firstName ?? '?', 0, 1) }}{{ mb_substr($user?->lastName ?? '', 0, 1) }}</span>
@endif
