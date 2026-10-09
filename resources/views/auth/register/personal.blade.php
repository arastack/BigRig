@php $relationships = config('admissions.relationships'); @endphp
<h2 class="text-3xl">About you</h2>
<p class="mt-1 text-ink-500">Enter your details exactly as they appear on your ID.</p>

<fieldset class="mt-8">
    <legend class="font-display text-xl font-bold uppercase tracking-wide text-ink-950">Personal details</legend>
    <div class="mt-4 grid gap-5 sm:grid-cols-3">
        <x-site.input name="firstName" label="First name" required autocomplete="given-name" />
        <x-site.input name="middleName" label="Middle name" autocomplete="additional-name" />
        <x-site.input name="lastName" label="Last name" required autocomplete="family-name" />
    </div>
    <div class="mt-5 grid gap-5 sm:grid-cols-3">
        <div>
            <span class="form-label">Gender <span class="text-red-600" aria-hidden="true">*</span></span>
            <div class="flex gap-2">
                @foreach (['Male', 'Female'] as $g)
                    <label class="flex-1">
                        <input type="radio" name="gender" value="{{ $g }}" class="peer sr-only" required @checked(old('gender') === $g)>
                        <span class="flex h-[46px] cursor-pointer items-center justify-center rounded-md border border-ink-200 text-sm font-semibold text-ink-600 peer-checked:border-ink-950 peer-checked:bg-ink-950 peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-brand-400">{{ $g }}</span>
                    </label>
                @endforeach
            </div>
            @error('gender')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <x-site.input name="date_of_birth" label="Date of birth" type="date" required autocomplete="bday" max="{{ now()->subYears(18)->toDateString() }}" hint="You must be 18 or older." />
        <x-site.input name="nin" label="NIN" required inputmode="numeric" pattern="\d{11}" maxlength="11" hint="11-digit National Identification Number" />
    </div>
</fieldset>

<fieldset class="mt-10">
    <legend class="font-display text-xl font-bold uppercase tracking-wide text-ink-950">Contact</legend>
    <div class="mt-4 grid gap-5 sm:grid-cols-2">
        <x-site.input name="mobileNumber" label="Phone number" type="tel" required autocomplete="tel" inputmode="tel" placeholder="0803 123 4567" />
        <x-site.input name="email" label="Email address" type="email" required autocomplete="email" hint="We'll send your application updates here." />
        <x-site.input name="address" label="Home address" required autocomplete="street-address" class="sm:col-span-2" />
        <x-site.input name="city" label="Town / city" required autocomplete="address-level2" />
        <div>
            <label for="state" class="form-label">State <span class="text-red-600" aria-hidden="true">*</span></label>
            <select id="state" name="state" required @class(['form-field', 'border-red-400' => $errors->has('state')])>
                <option value="">Select state</option>
                @foreach (\App\Support\Nigeria::STATES as $state)
                    <option @selected(old('state', 'Oyo') === $state)>{{ $state }}</option>
                @endforeach
            </select>
            @error('state')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>
</fieldset>

<fieldset class="mt-10">
    <legend class="font-display text-xl font-bold uppercase tracking-wide text-ink-950">Driving licence <span class="font-inter text-sm font-normal normal-case tracking-normal text-ink-400">— if you have one</span></legend>
    <p class="mt-1 text-sm text-ink-500">Required for the CDL and BRT/School Bus programmes.</p>
    <div class="mt-4 grid gap-5 sm:grid-cols-2">
        <x-site.input name="drivers_licence_number" label="Licence number" />
        <x-site.input name="drivers_licence_expiry" label="Expiry date" type="date" />
    </div>
</fieldset>

<fieldset class="mt-10">
    <legend class="font-display text-xl font-bold uppercase tracking-wide text-ink-950">Next of kin</legend>
    <p class="mt-1 text-sm text-ink-500">Who should we contact in an emergency?</p>
    <div class="mt-4 grid gap-5 sm:grid-cols-3">
        <x-site.input name="next_of_kin_name" label="Full name" required />
        <x-site.input name="next_of_kin_phone" label="Phone number" type="tel" required inputmode="tel" />
        <div>
            <label for="next_of_kin_relationship" class="form-label">Relationship <span class="text-red-600" aria-hidden="true">*</span></label>
            <select id="next_of_kin_relationship" name="next_of_kin_relationship" required class="form-field">
                <option value="">Select</option>
                @foreach ($relationships as $relationship)
                    <option @selected(old('next_of_kin_relationship') === $relationship)>{{ $relationship }}</option>
                @endforeach
            </select>
            @error('next_of_kin_relationship')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>
</fieldset>
