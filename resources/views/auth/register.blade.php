@php
    use App\Models\TrainingObjective;
    $courses = TrainingObjective::all();

    // After a failed submission, reopen the step that holds the first invalid field
    // instead of always dropping the applicant back on step one.
    $stepFields = [
        1 => ['firstName', 'middleName', 'lastName', 'gender', 'mobileNumber', 'email', 'password'],
        2 => ['weight', 'height', 'visual_impairment', 'hearing_aid', 'physical_disability', 'weed', 'alcohol', 'prescribed_medication', 'failed_drug_test'],
        3 => ['selected_objective', 'terms'],
    ];
    $initialStep = 1;
    if ($errors->any()) {
        foreach ($stepFields as $step => $fields) {
            $hasError = collect($errors->keys())->contains(fn ($key) => in_array(explode('.', $key)[0], $fields));
            if ($hasError) {
                $initialStep = $step;
                break;
            }
        }
    }
@endphp

@section('title', 'Register')
<x-authentication-layout>
    <div class="flex-1">
        <!-- Header -->
        <div class="flex items-center justify-between h-28 px-4 sm:px-6 lg:px-8">
            <!-- Logo -->
            <a class="block" href="{{ url('/home') }}">
                <!-- Light Theme Logo -->
                <img class="block dark:hidden w-40 h-40" src="{{ Storage::url($settings->light_theme_logo) }}" alt="Light Logo" />

                <!-- Dark Theme Logo -->
                <img class="hidden dark:block w-40 h-40" src="{{ Storage::url($settings->dark_theme_logo) }}" alt="Dark Logo" />
            </a>
            <div class="text-sm text-right space-y-1">
                <div>Have an account? <a class="font-medium text-violet-500 hover:text-violet-600 dark:hover:text-violet-400" href="{{ route('login') }}">Sign In</a></div>
                <div><a class="font-medium text-violet-500 hover:text-violet-600 dark:hover:text-violet-400" href="{{ route('register.form-pdf') }}">Download PDF Application Form</a></div>
            </div>
        </div>
    </div>

    <div class="px-4 py-4">
        <div class="max-w-5xl mx-auto">
            <x-validation-errors class="mb-4" />
            @if ($errors->any())
                <div class="mb-4 text-sm text-gray-500 dark:text-gray-400">For your security, please re-enter your password before submitting again.</div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('register') }}" novalidate x-data="registrationForm({{ $initialStep }})" @submit="submit($event)">
                @csrf

                <div>
                    <div data-step="1" x-show="step === 1" @if ($initialStep !== 1) x-cloak @endif>
                        <x-register.stepOne/>

                        <div class="flex items-center justify-between mt-6">
                            <button type="button" class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white ml-auto" @click="next()">Next Step -&gt;</button>
                        </div>
                    </div>

                    <div data-step="2" x-show="step === 2" @if ($initialStep !== 2) x-cloak @endif>
                        <x-register.stepTwo />

                        <div class="flex items-center justify-between mt-6">
                            <a class="text-sm underline hover:no-underline" href="javascript:void(0)" @click.prevent="back()">&lt;- Back</a>
                            <button type="button" class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white ml-auto" @click="next()">Next Step -&gt;</button>
                        </div>
                    </div>

                    <div data-step="3" x-show="step === 3" @if ($initialStep !== 3) x-cloak @endif>
                        <x-register.stepThree :courses="$courses"/>

                        <div x-show="objectiveError" x-cloak class="mt-4 px-4 py-2 rounded-lg text-sm bg-red-500 text-white">Please select at least one training objective.</div>

                        @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                            <div class="mt-6">
                                <label class="flex items-start">
                                    <input type="checkbox" class="form-checkbox mt-1" name="terms" id="terms" required />
                                    <span class="text-sm ml-2">
                                        {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                            'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="text-sm underline hover:no-underline">'.__('Terms of Service').'</a>',
                                            'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="text-sm underline hover:no-underline">'.__('Privacy Policy').'</a>',
                                        ]) !!}
                                    </span>
                                </label>
                            </div>
                        @endif

                        <div class="flex items-center justify-between mt-6">
                            <a class="text-sm underline hover:no-underline" href="javascript:void(0)" @click.prevent="back()">&lt;- Back</a>
                            <x-button id="payNowButton" class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white ml-auto disabled:opacity-60 disabled:cursor-wait" x-bind:disabled="submitting">
                                <span x-show="!submitting">{{ __('Proceed To Checkout') }} -&gt;</span>
                                <span x-show="submitting" x-cloak>{{ __('Submitting...') }}</span>
                            </x-button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('registrationForm', (initialStep) => ({
                step: initialStep,
                submitting: false,
                objectiveError: false,

                // Hidden steps are display:none, so native validation cannot show its
                // messages there. Validate each step ourselves before moving on.
                validateStep(n) {
                    const fields = this.$root.querySelectorAll(`[data-step="${n}"] input, [data-step="${n}"] textarea, [data-step="${n}"] select`);
                    for (const field of fields) {
                        if (!field.checkValidity()) {
                            this.goTo(n);
                            this.$nextTick(() => field.reportValidity());
                            return false;
                        }
                    }

                    if (n === 1) {
                        const password = this.$root.querySelector('#password');
                        const confirmation = this.$root.querySelector('#password_confirmation');
                        confirmation.setCustomValidity(password.value === confirmation.value ? '' : 'Passwords do not match.');
                        if (!confirmation.checkValidity()) {
                            this.goTo(n);
                            this.$nextTick(() => confirmation.reportValidity());
                            return false;
                        }
                    }

                    if (n === 3) {
                        this.objectiveError = this.$root.querySelectorAll('input[name="selected_objective[]"]:checked').length === 0;
                        if (this.objectiveError) {
                            this.goTo(n);
                            return false;
                        }
                    }

                    return true;
                },

                goTo(n) {
                    if (this.step !== n) {
                        this.step = n;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                },

                next() {
                    if (this.validateStep(this.step)) {
                        this.goTo(this.step + 1);
                    }
                },

                back() {
                    this.goTo(this.step - 1);
                },

                submit(event) {
                    if (this.submitting) {
                        event.preventDefault();
                        return;
                    }

                    for (const n of [1, 2, 3]) {
                        if (!this.validateStep(n)) {
                            event.preventDefault();
                            return;
                        }
                    }

                    // Guard against double submissions while the request is in flight.
                    this.submitting = true;
                },

                init() {
                    this.$root.addEventListener('change', (e) => {
                        if (e.target.name === 'selected_objective[]') {
                            this.objectiveError = false;
                        }
                        if (e.target.id === 'password_confirmation' || e.target.id === 'password') {
                            this.$root.querySelector('#password_confirmation').setCustomValidity('');
                        }
                    });

                    // Re-enable the button if the page is restored from the back/forward cache.
                    window.addEventListener('pageshow', () => { this.submitting = false; });
                },
            }));
        });
    </script>
</x-authentication-layout>
