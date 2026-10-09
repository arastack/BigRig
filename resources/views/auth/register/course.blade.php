<h2 class="text-3xl">Choose your course</h2>
<p class="mt-1 text-ink-500">Select one or more programmes. You can change your selection until you pay.</p>

<div class="mt-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900" x-show="objectiveError" x-cloak role="alert">Please choose at least one course to continue.</div>
@error('selected_objective')<p class="mt-4 text-sm text-red-600">{{ $message }}</p>@enderror

@foreach (config('admissions.combos', []) as $combo)
    <p class="mt-4 flex items-start gap-2 rounded-md bg-brand-50 px-4 py-3 text-sm text-ink-800 ring-1 ring-brand-300">
        <x-site.icon name="award" class="mt-0.5 h-4 w-4 shrink-0" />
        <span><strong>{{ $combo['name'] }}:</strong> {{ $combo['description'] }}</span>
    </p>
@endforeach

<div class="mt-6 space-y-4">
    @foreach ($courses as $course)
        <label class="relative flex cursor-pointer gap-4 rounded-lg border-2 p-4 transition"
               :class="selected.includes({{ $course->id }}) ? 'border-ink-950 bg-brand-50' : 'border-ink-100 hover:border-ink-300'">
            <input type="checkbox" name="selected_objective[]" value="{{ $course->id }}" x-model.number="selected" @change="objectiveError = false" class="sr-only">
            @if ($course->image_url)
                <img src="{{ img($course->image_url, 320) }}" alt="" class="hidden h-24 w-32 shrink-0 rounded-md object-cover sm:block" loading="lazy">
            @endif
            <span class="min-w-0 flex-1">
                <span class="block font-display text-2xl font-bold uppercase leading-tight text-ink-950">{{ $course->objective }}</span>
                <span class="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-sm text-ink-500">
                    <span>{{ $course->duration }} weeks</span>
                    <span>{{ $course->theory_session }} theory · {{ $course->practical_session }} practical</span>
                </span>
                <span class="mt-2 block font-display text-2xl font-bold text-ink-950">{{ $currency }}{{ number_format($course->price) }}</span>
                @if ($requirements = $course->requirementList())
                    <details class="mt-2 text-sm" @click.stop>
                        <summary class="cursor-pointer font-semibold text-ink-600 hover:text-ink-950">Entry requirements</summary>
                        <ul class="mt-2 list-inside list-disc space-y-1 text-ink-500">@foreach ($requirements as $r)<li>{{ $r }}</li>@endforeach</ul>
                    </details>
                @endif
            </span>
            <span class="mt-1 inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md border-2 transition"
                  :class="selected.includes({{ $course->id }}) ? 'border-ink-950 bg-ink-950 text-brand-400' : 'border-ink-300 bg-white text-transparent'" aria-hidden="true">
                <x-site.icon name="check" class="h-4 w-4" />
            </span>
        </label>
    @endforeach
</div>
