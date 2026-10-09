@php
    $phone = $settings->business_contact ?? null;
    $whatsapp = ($settings->show_whatsapp_support ?? false) && $settings->whatsapp_support ? preg_replace('/\D/', '', $settings->whatsapp_support) : null;
@endphp
<x-site.layout :title="page_text('contact.meta.title')" :description="page_text('contact.meta.description')">
    <x-site.page-hero :eyebrow="page_text('contact.hero.eyebrow')" :title="page_text('contact.hero.title')" :crumbs="['Contact' => null]"
        :lead="page_text('contact.hero.lead')" />

    <section class="section">
        <div class="container-site grid gap-12 lg:grid-cols-12">
            {{-- Contact details --}}
            <div class="space-y-4 lg:col-span-5">
                @if ($settings?->headquarters)
                    <div class="flex gap-4 rounded-lg border border-ink-100 p-6">
                        <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-md bg-brand-400 text-ink-950"><x-site.icon name="map-pin" class="h-6 w-6" /></span>
                        <div>
                            <h2 class="text-2xl">{{ page_text('contact.visit.title') }}</h2>
                            <p class="mt-1 text-ink-600">{{ $settings->headquarters }}</p>
                            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($settings->headquarters) }}" target="_blank" rel="noopener" class="link-underline mt-3 inline-block text-sm">{{ page_text('contact.visit.link') }}</a>
                        </div>
                    </div>
                @endif
                @if ($phone)
                    <div class="flex gap-4 rounded-lg border border-ink-100 p-6">
                        <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-md bg-brand-400 text-ink-950"><x-site.icon name="phone" class="h-6 w-6" /></span>
                        <div>
                            <h2 class="text-2xl">{{ page_text('contact.call.title') }}</h2>
                            <a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" class="mt-1 block text-ink-600 hover:text-ink-950">{{ $phone }}</a>
                            @if ($settings->secondary_contact)<a href="tel:{{ preg_replace('/[^\d+]/', '', $settings->secondary_contact) }}" class="block text-ink-600 hover:text-ink-950">{{ $settings->secondary_contact }}</a>@endif
                        </div>
                    </div>
                @endif
                @if ($settings?->business_email)
                    <div class="flex gap-4 rounded-lg border border-ink-100 p-6">
                        <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-md bg-brand-400 text-ink-950"><x-site.icon name="mail" class="h-6 w-6" /></span>
                        <div class="min-w-0">
                            <h2 class="text-2xl">{{ page_text('contact.email.title') }}</h2>
                            <a href="mailto:{{ $settings->business_email }}" class="mt-1 block break-all text-ink-600 hover:text-ink-950">{{ $settings->business_email }}</a>
                            @if ($settings->secondary_email)<a href="mailto:{{ $settings->secondary_email }}" class="block break-all text-ink-600 hover:text-ink-950">{{ $settings->secondary_email }}</a>@endif
                        </div>
                    </div>
                @endif
                @if ($whatsapp)
                    <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener" class="flex items-center justify-between gap-4 rounded-lg bg-ink-950 p-6 text-white hover:bg-ink-800">
                        <span>
                            <span class="block font-display text-2xl font-bold uppercase">{{ page_text('contact.whatsapp.title') }}</span>
                            <span class="text-sm text-ink-300">{{ page_text('contact.whatsapp.text') }}</span>
                        </span>
                        <x-site.icon name="arrow-up-right" class="h-6 w-6 text-brand-400" />
                    </a>
                @endif
            </div>

            {{-- Form --}}
            <div class="lg:col-span-7">
                <div class="rounded-lg border-2 border-ink-950 p-6 sm:p-10">
                    <h2 class="text-4xl">{{ page_text('contact.form.title') }}</h2>
                    <p class="mt-2 text-ink-500">Fields marked <span class="text-red-600">*</span> are required.</p>

                    @if ($errors->any())
                        <div class="mt-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900" role="alert">Please check the highlighted fields and try again.</div>
                    @endif

                    <form method="POST" action="{{ route('contact.store') }}" class="mt-8 grid gap-5 sm:grid-cols-2" x-data="{ sending: false }" @submit="sending = true">
                        @csrf
                        {{-- Honeypot field for bots --}}
                        <div class="hidden" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

                        <div>
                            <label for="name" class="form-label">Full name <span class="text-red-600">*</span></label>
                            <input id="name" name="name" type="text" required autocomplete="name" value="{{ old('name') }}" @class(['form-field', 'border-red-400' => $errors->has('name')])>
                            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="email" class="form-label">Email <span class="text-red-600">*</span></label>
                            <input id="email" name="email" type="email" required autocomplete="email" value="{{ old('email') }}" @class(['form-field', 'border-red-400' => $errors->has('email')])>
                            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="phone" class="form-label">Phone</label>
                            <input id="phone" name="phone" type="tel" autocomplete="tel" inputmode="tel" value="{{ old('phone') }}" class="form-field">
                        </div>
                        <div>
                            <label for="course_id" class="form-label">Course of interest</label>
                            <select id="course_id" name="course_id" class="form-field">
                                <option value="">Not sure yet</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}" @selected(old('course_id', request('course')) == $course->id)>{{ $course->objective }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="subject" class="form-label">Subject</label>
                            <input id="subject" name="subject" type="text" value="{{ old('subject') }}" class="form-field" placeholder="e.g. Next intake date">
                        </div>
                        <div class="sm:col-span-2">
                            <label for="message" class="form-label">Message <span class="text-red-600">*</span></label>
                            <textarea id="message" name="message" rows="6" required minlength="10" @class(['form-field', 'border-red-400' => $errors->has('message')])>{{ old('message') }}</textarea>
                            @error('message')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="flex flex-col gap-4 sm:col-span-2 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-xs text-ink-400">We use your details only to answer your enquiry. See our <a href="{{ route('privacy') }}" class="underline">privacy policy</a>.</p>
                            <button class="btn-brand shrink-0" :disabled="sending">
                                <span x-show="!sending">{{ page_text('contact.form.button') }}</span><span x-show="sending" x-cloak>Sending…</span>
                                <x-site.icon name="send" class="h-5 w-5" />
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    @if ($settings?->headquarters)
        <section aria-label="Map" class="h-96 bg-ink-100">
            <iframe class="h-full w-full grayscale" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Map to {{ $settings->site_name }}"
                    src="https://www.google.com/maps?q={{ urlencode($settings->headquarters) }}&output=embed"></iframe>
        </section>
    @endif
</x-site.layout>
