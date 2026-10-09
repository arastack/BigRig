@props(['course', 'featured' => false])
@php $icon = match (true) {
    str_contains(strtolower($course->objective), 'forklift') => 'forklift',
    str_contains(strtolower($course->objective), 'bus') => 'bus',
    str_contains(strtolower($course->objective), 'car') => 'car',
    default => 'truck',
}; @endphp
<article {{ $attributes->merge(['class' => 'group relative flex flex-col overflow-hidden rounded-lg border border-ink-100 bg-white transition hover:-translate-y-1 hover:border-ink-950 hover:shadow-[6px_6px_0_0_theme(colors.brand.400)]']) }}>
    <div class="relative aspect-[16/10] overflow-hidden bg-ink-100">
        @if ($course->image_url)
            <img src="{{ img($course->image_url, 720) }}" alt="{{ $course->objective }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy" width="720" height="450">
        @endif
        <span class="absolute left-4 top-4 inline-flex items-center gap-1.5 rounded bg-ink-950 px-2.5 py-1 font-display text-sm font-semibold uppercase tracking-wider text-brand-400">
            <x-site.icon :name="$icon" class="h-4 w-4" /> {{ page_text('course_card.weeks', [':weeks' => $course->duration]) }}
        </span>
    </div>
    <div class="flex flex-1 flex-col p-6">
        <h3 class="text-2xl sm:text-[1.75rem]">
            <a href="{{ route('course', $course->slug) }}" class="after:absolute after:inset-0">{{ $course->objective }}</a>
        </h3>
        <dl class="mt-4 grid grid-cols-3 gap-2 border-y border-ink-100 py-3 text-center">
            <div><dt class="text-[11px] font-semibold uppercase tracking-wider text-ink-400">{{ page_text('course_card.theory') }}</dt><dd class="font-display text-xl font-bold text-ink-950">{{ $course->theory_session ?: '—' }}</dd></div>
            <div class="border-x border-ink-100"><dt class="text-[11px] font-semibold uppercase tracking-wider text-ink-400">{{ page_text('course_card.practical') }}</dt><dd class="font-display text-xl font-bold text-ink-950">{{ $course->practical_session ?: '—' }}</dd></div>
            <div><dt class="text-[11px] font-semibold uppercase tracking-wider text-ink-400">{{ page_text('course_card.exam') }}</dt><dd class="font-display text-xl font-bold text-ink-950">{{ $course->examination ?: page_text('course_card.exam_default') }}</dd></div>
        </dl>
        <div class="mt-auto flex items-end justify-between gap-4 pt-5">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-ink-400">{{ page_text('course_card.tuition') }}</p>
                <p class="font-display text-3xl font-bold text-ink-950">{{ $settings->base_currency ?? '₦' }}{{ number_format($course->price) }}</p>
            </div>
            <span class="inline-flex h-11 w-11 items-center justify-center rounded-md bg-ink-950 text-brand-400 transition group-hover:bg-brand-400 group-hover:text-ink-950" aria-hidden="true">
                <x-site.icon name="arrow-up-right" class="h-5 w-5" />
            </span>
        </div>
    </div>
</article>
