@php
    // Internal role names (SuperAdmin, IT Consultant…) are not public job titles
    $title = $member->roles->firstWhere('portal', 'instructor')?->role_name ?? 'Management';
@endphp
<article class="group">
    <div class="relative aspect-[4/5] overflow-hidden rounded-lg bg-ink-100">
        @if ($member->profile_photo_path)
            <img src="{{ img($member->profile_photo_path, 600) }}" alt="{{ $member->full_name }}" class="h-full w-full object-cover grayscale transition duration-500 group-hover:scale-105 group-hover:grayscale-0" loading="lazy" width="600" height="750">
        @else
            <div class="flex h-full w-full items-center justify-center bg-ink-950 font-display text-8xl font-extrabold text-brand-400" aria-hidden="true">{{ mb_substr($member->firstName, 0, 1) }}{{ mb_substr($member->lastName, 0, 1) }}</div>
        @endif
        <div class="absolute inset-x-0 bottom-0 h-1.5 origin-left scale-x-0 bg-brand-400 transition duration-300 group-hover:scale-x-100"></div>
    </div>
    <h3 class="mt-4 text-2xl">{{ $member->firstName }} {{ $member->lastName }}</h3>
    @if ($title)<p class="text-sm font-semibold uppercase tracking-wider text-ink-400">{{ $title }}</p>@endif
</article>
