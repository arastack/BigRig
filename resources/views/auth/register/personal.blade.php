@php $relationships = \App\Support\AdmissionSettings::relationships(); @endphp
<h2 class="text-3xl">{{ page_text('register.personal.title') }}</h2>
<p class="mt-1 text-ink-500">{{ page_text('register.personal.lead') }}</p>

<fieldset class="mt-8">
    <legend class="font-display text-xl font-bold uppercase tracking-wide text-ink-950">{{ page_text('register.personal.legend') }}</legend>
    <div class="mt-4 grid gap-5 sm:grid-cols-3">
        <x-site.input name="firstName" :label="page_text('register.personal.first_name')" required autocomplete="given-name" />
        <x-site.input name="middleName" :label="page_text('register.personal.middle_name')" autocomplete="additional-name" />
        <x-site.input name="lastName" :label="page_text('register.personal.last_name')" required autocomplete="family-name" />
    </div>
    <div class="mt-5 grid gap-5 sm:grid-cols-3">
        <div>
            <span class="form-label">{{ page_text('register.personal.gender') }} <span class="text-red-600" aria-hidden="true">*</span></span>
            <div class="flex gap-2">
                @foreach (['Male' => page_text('register.personal.male'), 'Female' => page_text('register.personal.female')] as $g => $genderLabel)
                    <label class="flex-1">
                        <input type="radio" name="gender" value="{{ $g }}" class="peer sr-only" required @checked(old('gender') === $g)>
                        <span class="flex h-[46px] cursor-pointer items-center justify-center rounded-md border border-ink-200 text-sm font-semibold text-ink-600 peer-checked:border-ink-950 peer-checked:bg-ink-950 peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-brand-400">{{ $genderLabel }}</span>
                    </label>
                @endforeach
            </div>
            @error('gender')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <x-site.input name="date_of_birth" :label="page_text('register.personal.dob')" type="date" required autocomplete="bday" max="{{ now()->subYears(18)->toDateString() }}" :hint="page_text('register.personal.dob_hint')" />
        <x-site.input name="nin" :label="page_text('register.personal.nin')" required inputmode="numeric" pattern="\d{11}" maxlength="11" :hint="page_text('register.personal.nin_hint')" />
    </div>
</fieldset>

<fieldset class="mt-10">
    <legend class="font-display text-xl font-bold uppercase tracking-wide text-ink-950">{{ page_text('register.contact.legend') }}</legend>
    <div class="mt-4 grid gap-5 sm:grid-cols-2">
        <x-site.input name="mobileNumber" :label="page_text('register.contact.phone')" type="tel" required autocomplete="tel" inputmode="tel" :placeholder="page_text('register.contact.phone_placeholder')" />
        <x-site.input name="email" :label="page_text('register.contact.email')" type="email" required autocomplete="email" :hint="page_text('register.contact.email_hint')" />
        <x-site.input name="address" :label="page_text('register.contact.address')" required autocomplete="street-address" class="sm:col-span-2" />
        <x-site.input name="city" :label="page_text('register.contact.city')" required autocomplete="address-level2" />
        <div>
            <label for="state" class="form-label">{{ page_text('register.contact.state') }} <span class="text-red-600" aria-hidden="true">*</span></label>
            <select id="state" name="state" required @class(['form-field', 'border-red-400' => $errors->has('state')])>
                <option value="">{{ page_text('register.contact.state_placeholder') }}</option>
                @foreach (\App\Support\Nigeria::STATES as $state)
                    <option @selected(old('state', 'Oyo') === $state)>{{ $state }}</option>
                @endforeach
            </select>
            @error('state')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>
</fieldset>

<fieldset class="mt-10">
    <legend class="font-display text-xl font-bold uppercase tracking-wide text-ink-950">{{ page_text('register.licence.legend') }} <span class="font-inter text-sm font-normal normal-case tracking-normal text-ink-400">{{ page_text('register.licence.legend_note') }}</span></legend>
    <p class="mt-1 text-sm text-ink-500">{{ page_text('register.licence.note') }}</p>
    <div class="mt-4 grid gap-5 sm:grid-cols-2">
        <x-site.input name="drivers_licence_number" :label="page_text('register.licence.number')" />
        <x-site.input name="drivers_licence_expiry" :label="page_text('register.licence.expiry')" type="date" />
    </div>
</fieldset>

<fieldset class="mt-10">
    <legend class="font-display text-xl font-bold uppercase tracking-wide text-ink-950">{{ page_text('register.kin.legend') }}</legend>
    <p class="mt-1 text-sm text-ink-500">{{ page_text('register.kin.note') }}</p>
    <div class="mt-4 grid gap-5 sm:grid-cols-3">
        <x-site.input name="next_of_kin_name" :label="page_text('register.kin.name')" required />
        <x-site.input name="next_of_kin_phone" :label="page_text('register.kin.phone')" type="tel" required inputmode="tel" />
        <div>
            <label for="next_of_kin_relationship" class="form-label">{{ page_text('register.kin.relationship') }} <span class="text-red-600" aria-hidden="true">*</span></label>
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
