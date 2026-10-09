<h2 class="text-3xl">Documents &amp; account</h2>
<p class="mt-1 text-ink-500">Upload your passport photograph and create a password to track your application.</p>

<div class="mt-8 grid gap-6 sm:grid-cols-[10rem_1fr] sm:items-start">
    <div class="relative mx-auto flex aspect-[4/5] w-40 items-center justify-center overflow-hidden rounded-lg border-2 border-dashed border-ink-300 bg-ink-50 text-center">
        <template x-if="photoPreview"><img :src="photoPreview" alt="Passport photograph preview" class="absolute inset-0 h-full w-full object-cover"></template>
        <span x-show="!photoPreview" class="px-3 text-xs text-ink-400"><x-site.icon name="user" class="mx-auto mb-2 h-8 w-8" />Passport photograph</span>
    </div>
    <div>
        <label for="passport_photo" class="form-label">Passport photograph <span class="text-red-600" aria-hidden="true">*</span></label>
        <input id="passport_photo" name="passport_photo" type="file" accept="image/jpeg,image/png,image/webp" required @change="previewPhoto($event)"
               class="block w-full text-sm text-ink-600 file:mr-4 file:rounded-md file:border-0 file:bg-ink-950 file:px-4 file:py-2.5 file:font-semibold file:text-white hover:file:bg-ink-800">
        <ul class="mt-3 list-inside list-disc space-y-1 text-xs text-ink-500">
            <li>Recent, in colour, with a plain light background</li>
            <li>Face clearly visible, no sunglasses or caps</li>
            <li>JPG or PNG, up to 3 MB</li>
        </ul>
        @error('passport_photo')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<div class="mt-8 grid gap-5 sm:grid-cols-2">
    @foreach (['drivers_licence_file' => "Driver's licence (front)", 'nin_slip_file' => 'NIN slip or card'] as $field => $label)
        <div>
            <label for="{{ $field }}" class="form-label">{{ $label }} <span class="font-normal text-ink-400">— optional</span></label>
            <input id="{{ $field }}" name="{{ $field }}" type="file" accept="application/pdf,image/jpeg,image/png,image/webp"
                   class="block w-full rounded-md border border-ink-200 text-sm text-ink-600 file:mr-4 file:border-0 file:bg-ink-100 file:px-4 file:py-2.5 file:font-semibold file:text-ink-900 hover:file:bg-ink-200">
            <p class="mt-1.5 text-xs text-ink-400">PDF or image, up to 5 MB. You can also upload this later.</p>
            @error($field)<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    @endforeach
</div>

<fieldset class="mt-10">
    <legend class="font-display text-xl font-bold uppercase tracking-wide text-ink-950">Create your password</legend>
    <div class="mt-4 grid gap-5 sm:grid-cols-2">
        <x-site.input name="password" label="Password" type="password" required autocomplete="new-password" minlength="8" hint="At least 8 characters." />
        <x-site.input name="password_confirmation" label="Confirm password" type="password" required autocomplete="new-password" errorKey="password_confirmation" />
    </div>
</fieldset>

<label class="mt-8 flex gap-3 rounded-lg bg-ink-50 p-4">
    <input type="checkbox" name="terms" value="1" required @checked(old('terms')) class="mt-0.5 h-5 w-5 shrink-0 rounded border-ink-300 text-ink-950 focus:ring-brand-400">
    <span class="text-sm text-ink-700">I confirm the information in this application is accurate, and I agree to the <a href="{{ route('terms') }}" target="_blank" class="underline">terms &amp; conditions</a> and <a href="{{ route('privacy') }}" target="_blank" class="underline">privacy policy</a>. <span class="text-red-600" aria-hidden="true">*</span></span>
</label>
@error('terms')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
