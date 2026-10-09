@php
    $options = config('admissions.options');
    $labels = config('admissions.labels');
    $groups = [
        'visual_impairment' => ['Do you have any vision condition?', 'Glasses or contact lenses are fine — tell us so instructors know.'],
        'hearing_aid' => ['Do you use a hearing aid?', null],
        'physical_disability' => ['Do you have any of these conditions?', 'Choose the one that applies, or None.'],
        'weed' => ['Do you use marijuana (weed), cocaine or other recreational drugs?', 'All students take a pre-employment drug screening.'],
        'alcohol' => ['How often do you drink alcohol?', null],
    ];
@endphp
<h2 class="text-3xl">Health declaration</h2>
<p class="mt-1 text-ink-500">Operating heavy vehicles safely depends on your health. Answer honestly — this is confidential and only seen by admissions and training staff.</p>

<div class="mt-8 grid gap-5 sm:grid-cols-2">
    <x-site.input name="weight" label="Weight (kg)" inputmode="decimal" placeholder="e.g. 75" />
    <x-site.input name="height" label="Height (ft)" inputmode="decimal" placeholder="e.g. 5.8" />
</div>

<div class="mt-8 space-y-8">
    @foreach ($groups as $field => [$question, $help])
        <fieldset>
            <legend class="font-semibold text-ink-900">{{ $question }} <span class="text-red-600" aria-hidden="true">*</span></legend>
            @if ($help)<p class="mt-0.5 text-sm text-ink-400">{{ $help }}</p>@endif
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($options[$field] as $value)
                    <label>
                        <input type="radio" name="{{ $field }}" value="{{ $value }}" class="peer sr-only" required @checked(old($field) === $value)>
                        <span class="inline-flex cursor-pointer items-center rounded-full border border-ink-200 px-4 py-2 text-sm font-medium text-ink-700 transition hover:border-ink-400 peer-checked:border-ink-950 peer-checked:bg-ink-950 peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-brand-400">{{ $labels[$field][$value] ?? $value }}</span>
                    </label>
                @endforeach
            </div>
            @error($field)<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </fieldset>
    @endforeach

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="prescribed_medication" class="form-label">Are you taking any prescribed medication?</label>
            <textarea id="prescribed_medication" name="prescribed_medication" rows="3" class="form-field" placeholder="Leave blank if none">{{ old('prescribed_medication') }}</textarea>
        </div>
        <div>
            <label for="failed_drug_test" class="form-label">Have you ever failed a drug test?</label>
            <textarea id="failed_drug_test" name="failed_drug_test" rows="3" class="form-field" placeholder="If yes, tell us when and why">{{ old('failed_drug_test') }}</textarea>
        </div>
    </div>

    <label class="flex gap-3 rounded-lg bg-ink-50 p-4">
        <input type="checkbox" name="health_consent" value="1" required @checked(old('health_consent')) class="mt-0.5 h-5 w-5 shrink-0 rounded border-ink-300 text-ink-950 focus:ring-brand-400">
        <span class="text-sm text-ink-700">I confirm this health information is true and complete, and I consent to BigRig processing it to assess my fitness to train safely, as described in the <a href="{{ route('privacy') }}" target="_blank" class="underline">privacy policy</a>. <span class="text-red-600" aria-hidden="true">*</span></span>
    </label>
    @error('health_consent')<p class="-mt-6 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
