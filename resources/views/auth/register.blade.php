@php
    use App\Models\TrainingObjective;

    $courses = TrainingObjective::orderBy('price')->get();
    $currency = $settings->base_currency ?? '₦';

    // Pre-select a course when arriving from a course page (?course=ID)
    $selected = collect(old('selected_objective', request()->filled('course') ? [request('course')] : []))
        ->map(fn ($id) => (int) $id)->filter(fn ($id) => $courses->contains('id', $id))->values();

    // After a failed submission, reopen the step that holds the first invalid field
    $stepFields = [
        1 => ['selected_objective'],
        2 => ['firstName', 'middleName', 'lastName', 'gender', 'date_of_birth', 'mobileNumber', 'email', 'address', 'city', 'state', 'nin', 'drivers_licence_number', 'drivers_licence_expiry', 'next_of_kin_name', 'next_of_kin_phone', 'next_of_kin_relationship'],
        3 => ['weight', 'height', 'visual_impairment', 'hearing_aid', 'physical_disability', 'weed', 'alcohol', 'prescribed_medication', 'failed_drug_test', 'health_consent'],
        4 => ['passport_photo', 'drivers_licence_file', 'nin_slip_file', 'password', 'terms'],
    ];
    $initialStep = 1;
    if ($errors->any()) {
        $initialStep = collect($stepFields)->search(fn ($fields) => collect($errors->keys())->contains(fn ($key) => in_array(explode('.', $key)[0], $fields))) ?: 1;
    }

    $courseData = $courses->map(fn ($c) => ['id' => $c->id, 'name' => $c->objective, 'price' => (float) $c->price, 'weeks' => (int) $c->duration])->values();
    $steps = ['Course', 'About you', 'Health', 'Documents'];
    $combos = \App\Support\Pricing::combosForBrowser($courses);
    $vatPercent = \App\Support\Pricing::vatPercent();
@endphp

<x-site.auth title="Apply" :wide="true">
    <div class="on-dark bg-ink-950 text-white">
        <div class="container-site py-10 sm:py-12">
            <p class="eyebrow">Online application</p>
            <h1 class="mt-3 text-5xl text-white sm:text-6xl">Apply to BigRig</h1>
            <p class="mt-3 max-w-2xl text-ink-300">Takes about 10 minutes. Have your NIN and a passport photograph ready. Prefer paper? <a href="{{ route('register.form-pdf') }}" class="font-semibold text-brand-400 underline underline-offset-4">Download the PDF form</a>.</p>
        </div>
    </div>

    @unless ($activeBatch)
        <div class="container-site mt-8">
            <div class="flex gap-3 rounded-lg border border-brand-500 bg-brand-50 px-5 py-4 text-ink-900" role="alert">
                <x-site.icon name="calendar" class="h-6 w-6 shrink-0" />
                <p><strong>Admissions are currently closed.</strong> You can still look around, but applications will open with the next intake. <a href="{{ route('contact') }}" class="underline">Contact us</a> to be notified.</p>
            </div>
        </div>
    @endunless

    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" novalidate
          x-data="application({{ $initialStep }}, @js($courseData), @js($selected), @js($combos), @js($vatPercent))" @submit="submit($event)"
          class="container-site mt-8 grid gap-8 lg:grid-cols-12">
        @csrf

        <div class="lg:col-span-8">
            {{-- Stepper --}}
            <ol class="mb-8 grid grid-cols-4 gap-2" aria-label="Application progress">
                @foreach ($steps as $i => $label)
                    @php $n = $i + 1; @endphp
                    <li>
                        <button type="button" @click="jump({{ $n }})" class="group w-full text-left" :aria-current="step === {{ $n }} ? 'step' : null">
                            <span class="block h-1.5 rounded-full transition" :class="step >= {{ $n }} ? 'bg-ink-950' : 'bg-ink-200'"></span>
                            <span class="mt-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider sm:text-sm" :class="step === {{ $n }} ? 'text-ink-950' : 'text-ink-400'">
                                <span class="hidden h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs sm:inline-flex" :class="step > {{ $n }} ? 'bg-brand-400 text-ink-950' : (step === {{ $n }} ? 'bg-ink-950 text-white' : 'bg-ink-200 text-ink-500')">
                                    <span x-show="step <= {{ $n }}">{{ $n }}</span><x-site.icon name="check" class="h-3.5 w-3.5" x-show="step > {{ $n }}" x-cloak />
                                </span>
                                {{ $label }}
                            </span>
                        </button>
                    </li>
                @endforeach
            </ol>

            <x-site.status :summary="false" />
            @if ($errors->any())
                <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900" role="alert">
                    <p class="font-semibold">Some details need attention — we've opened the step that needs fixing.</p>
                    <p class="mt-1">For your security, please re-enter your password and re-attach your passport photograph and any documents.</p>
                </div>
            @endif

            <div class="rounded-lg border border-ink-100 bg-white p-6 shadow-sm sm:p-8">
                <section data-step="1" x-show="step === 1" @if ($initialStep !== 1) x-cloak @endif>@include('auth.register.course')</section>
                <section data-step="2" x-show="step === 2" @if ($initialStep !== 2) x-cloak @endif>@include('auth.register.personal')</section>
                <section data-step="3" x-show="step === 3" @if ($initialStep !== 3) x-cloak @endif>@include('auth.register.health')</section>
                <section data-step="4" x-show="step === 4" @if ($initialStep !== 4) x-cloak @endif>@include('auth.register.documents')</section>

                <div class="mt-10 flex items-center justify-between gap-4 border-t border-ink-100 pt-6">
                    <button type="button" class="btn-outline btn-sm" x-show="step > 1" x-cloak @click="back()">Back</button>
                    <span x-show="step === 1"></span>
                    <button type="button" class="btn-dark" x-show="step < 4" @click="next()">Continue <x-site.icon name="arrow-right" class="h-5 w-5" /></button>
                    <button type="submit" class="btn-brand" x-show="step === 4" x-cloak :disabled="submitting || {{ $activeBatch ? 'false' : 'true' }}">
                        <span x-show="!submitting">Submit application</span><span x-show="submitting" x-cloak>Submitting…</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Summary --}}
        <aside class="lg:col-span-4">
            <div class="sticky top-6 overflow-hidden rounded-lg border-2 border-ink-950 bg-white">
                <div class="bg-ink-950 px-6 py-4"><h2 class="text-2xl text-white">Your selection</h2></div>
                <div class="p-6">
                    <template x-if="chosen().length === 0">
                        <p class="text-sm text-ink-500">No course selected yet.</p>
                    </template>
                    <ul class="space-y-3">
                        <template x-for="course in chosen()" :key="course.id">
                            <li class="flex justify-between gap-4 text-sm">
                                <span class="font-medium text-ink-800" x-text="course.name"></span>
                                <span class="shrink-0 font-semibold text-ink-950" x-text="money(course.price)"></span>
                            </li>
                        </template>
                    </ul>
                    <dl class="mt-5 space-y-2 border-t border-ink-100 pt-4 text-sm">
                        <div class="flex justify-between"><dt class="text-ink-500">Subtotal</dt><dd x-text="money(subtotal())"></dd></div>
                        <div class="flex justify-between text-green-700" x-show="discount() > 0" x-cloak><dt x-text="combo()?.name"></dt><dd x-text="'−' + money(discount())"></dd></div>
                        <div class="flex justify-between"><dt class="text-ink-500">VAT ({{ \App\Support\Pricing::vatLabel() }}%)</dt><dd x-text="money(vat())"></dd></div>
                        <div class="flex justify-between border-t border-ink-100 pt-3 text-base"><dt class="font-semibold text-ink-950">Total due</dt><dd class="font-display text-2xl font-bold text-ink-950" x-text="money(subtotal() - discount() + vat())"></dd></div>
                    </dl>
                    <p class="mt-4 text-xs text-ink-400" x-show="weeks() > 0" x-text="'Programme length: ' + weeks() + ' weeks' + (chosen().length > 1 ? ' if taken one after another' : '')"></p>
                    @if ($activeBatch)
                        <p class="mt-4 flex items-center gap-2 rounded-md bg-ink-50 px-3 py-2 text-xs font-medium text-ink-700"><x-site.icon name="calendar" class="h-4 w-4" /> Intake: {{ $activeBatch->batch_name }}</p>
                    @endif
                    <p class="mt-4 text-xs text-ink-400">You pay after submitting. Your place is confirmed when payment is received.</p>
                </div>
            </div>
        </aside>
    </form>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('application', (initialStep, courses, selected, combos, vatPercent) => ({
                step: initialStep,
                submitting: false,
                courses,
                selected,
                combos,
                vatPercent,
                objectiveError: false,
                photoPreview: null,

                chosen() { return this.courses.filter(c => this.selected.includes(c.id)) },
                subtotal() { return this.chosen().reduce((sum, c) => sum + c.price, 0) },
                // Best package discount the selection qualifies for (mirrors App\\Support\\Pricing)
                combo() {
                    const applies = this.combos.filter(c => c.groups.every(ids => ids.some(id => this.selected.includes(id))));
                    return applies.sort((a, b) => b.discount - a.discount)[0] || null;
                },
                discount() { return this.combo() ? Math.min(this.combo().discount, this.subtotal()) : 0 },
                vat() { return Math.round((this.subtotal() - this.discount()) * this.vatPercent) / 100 },
                weeks() { return this.chosen().reduce((sum, c) => sum + c.weeks, 0) },
                money(n) { return @js($currency) + n.toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) },

                previewPhoto(event) {
                    const file = event.target.files[0];
                    this.photoPreview = file ? URL.createObjectURL(file) : null;
                },

                // Hidden steps are display:none, so native validation can't show messages there; check each step ourselves.
                validateStep(n) {
                    const section = this.$root.querySelector(`[data-step="${n}"]`);

                    if (n === 1) {
                        this.objectiveError = this.selected.length === 0;
                        if (this.objectiveError) { this.goTo(1); return false; }
                    }

                    if (n === 4) {
                        const password = this.$root.querySelector('#password');
                        const confirmation = this.$root.querySelector('#password_confirmation');
                        confirmation.setCustomValidity(password.value === confirmation.value ? '' : 'Passwords do not match.');
                    }

                    for (const field of section.querySelectorAll('input, select, textarea')) {
                        if (!field.checkValidity()) {
                            this.goTo(n);
                            this.$nextTick(() => { field.reportValidity(); });
                            return false;
                        }
                    }
                    return true;
                },
                goTo(n) {
                    if (this.step !== n) {
                        this.step = n;
                        this.$nextTick(() => this.$root.scrollIntoView({ behavior: 'smooth', block: 'start' }));
                    }
                },
                next() { if (this.validateStep(this.step)) this.goTo(this.step + 1) },
                back() { this.goTo(this.step - 1) },
                jump(n) {
                    if (n < this.step) return this.goTo(n);
                    for (let s = this.step; s < n; s++) { if (!this.validateStep(s)) return; }
                    this.goTo(n);
                },
                submit(event) {
                    if (this.submitting) return event.preventDefault();
                    for (const n of [1, 2, 3, 4]) {
                        if (!this.validateStep(n)) return event.preventDefault();
                    }
                    this.submitting = true;
                },
                init() {
                    this.$root.addEventListener('input', (e) => { if (e.target.id === 'password_confirmation' || e.target.id === 'password') this.$root.querySelector('#password_confirmation').setCustomValidity('') });
                    window.addEventListener('pageshow', () => { this.submitting = false });
                },
            }));
        });
    </script>
</x-site.auth>
