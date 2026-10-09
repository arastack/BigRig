<?php

namespace App\Actions\Fortify;

use App\Models\ApplicantDocument;
use App\Models\EnrolmentBatch;
use App\Models\Medical;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\TrainingObjective;
use App\Models\User;
use App\Notifications\ApplicationReceived;
use App\Support\AdmissionSettings;
use App\Support\Nigeria;
use App\Support\Pricing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered applicant.
     *
     * @param  array<string, mixed>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, self::rules($this->passwordRules()), self::messages(), self::attributes())->validate();

        $activeBatch = EnrolmentBatch::where('active_batch', true)->first();

        if (! $activeBatch) {
            throw ValidationException::withMessages([
                'selected_objective' => 'Enrolment is currently closed. Please check back later or contact us.',
            ]);
        }

        $storedFiles = [];

        try {
            // Create the user and all related records atomically, so a failure part-way
            // does not leave an orphaned account that blocks the applicant from retrying.
            $user = DB::transaction(function () use ($input, $activeBatch, &$storedFiles) {
                $user = User::create([
                    'enrolment_batch_id' => $activeBatch->id,
                    'firstName' => $input['firstName'],
                    'middleName' => $input['middleName'] ?? null,
                    'lastName' => $input['lastName'],
                    'gender' => $input['gender'],
                    'date_of_birth' => $input['date_of_birth'],
                    'mobileNumber' => $input['mobileNumber'],
                    'email' => $input['email'],
                    'address' => $input['address'],
                    'city' => $input['city'],
                    'state' => $input['state'],
                    'nin' => $input['nin'],
                    'drivers_licence_number' => $input['drivers_licence_number'] ?? null,
                    'drivers_licence_expiry' => $input['drivers_licence_expiry'] ?? null,
                    'next_of_kin_name' => $input['next_of_kin_name'],
                    'next_of_kin_phone' => $input['next_of_kin_phone'],
                    'next_of_kin_relationship' => $input['next_of_kin_relationship'],
                    'password' => Hash::make($input['password']),
                    'user_active' => 0,
                ]);

                $user->roles()->attach(Role::STUDENT_ROLE_ID);

                Medical::create([
                    'user_id' => $user->id,
                    'weight' => $input['weight'] ?? null,
                    'height' => $input['height'] ?? null,
                    'visual_impairment' => $input['visual_impairment'] ?? null,
                    'hearing_aid' => $input['hearing_aid'] ?? null,
                    'physical_disability' => $input['physical_disability'] ?? null,
                    'weed' => $input['weed'] ?? null,
                    'alcohol' => $input['alcohol'] ?? null,
                    'prescribed_medication' => $input['prescribed_medication'] ?? null,
                    'failed_drug_test' => $input['failed_drug_test'] ?? null,
                    'consent_given_at' => now(),
                ]);

                // The passport photograph doubles as the account photo shown to staff
                $photo = $input['passport_photo']->store('profile-photos', 'public');
                $storedFiles[] = ['public', $photo];
                $user->forceFill(['profile_photo_path' => $photo])->save();

                foreach (['drivers_licence', 'nin_slip'] as $type) {
                    if (($input[$type . '_file'] ?? null) instanceof \Illuminate\Http\UploadedFile) {
                        $document = ApplicantDocument::storeFor($user, $type, $input[$type . '_file']);
                        $storedFiles[] = [ApplicantDocument::DISK, $document->path];
                    }
                }

                self::createSubscription($user, $input['selected_objective']);

                return $user;
            });
        } catch (\Throwable $e) {
            // Files are not covered by the transaction; remove anything already saved
            foreach ($storedFiles as [$disk, $path]) {
                Storage::disk($disk)->delete($path);
            }

            throw $e;
        }

        $subscription = $user->subscriptions()->latest()->first();
        after_response(fn () => $user->notify(new ApplicationReceived($subscription)), 'Application confirmation email');

        return $user;
    }

    /**
     * Price the selected courses and open a pending subscription.
     */
    public static function createSubscription(User $user, array $courseIds): Subscription
    {
        $courseIds = array_values(array_unique(array_map('intval', $courseIds)));
        $quote = Pricing::quote($courseIds);

        return Subscription::create([
            'user_id' => $user->id,
            'payment_reference' => null,
            'objectives' => $courseIds,
            'subtotal' => $quote['subtotal'],
            'discount' => $quote['discount'],
            'tax' => $quote['tax'],
            'total_amount' => $quote['total'],
            'payment_status' => 'pending',
            'payment_method' => null,
        ]);
    }

    public static function rules(array $passwordRules): array
    {
        // Health answers follow Admin → Admissions settings; a question not asked online is skipped
        $asked = collect(AdmissionSettings::healthQuestions('web'))->keyBy('field');
        $health = fn (string $field) => $asked->has($field)
            ? [$asked[$field]['required'] ? 'required' : 'nullable', Rule::in(AdmissionSettings::optionValues($field))]
            : ['nullable', Rule::in(AdmissionSettings::optionValues($field))];

        return [
            // Course
            'selected_objective' => ['required', 'array', 'min:1'],
            'selected_objective.*' => ['integer', 'exists:training_objectives,id'],

            // About you
            'firstName' => ['required', 'string', 'max:100'],
            'middleName' => ['nullable', 'string', 'max:100'],
            'lastName' => ['required', 'string', 'max:100'],
            'gender' => ['required', Rule::in(['Male', 'Female'])],
            'date_of_birth' => ['required', 'date', 'before_or_equal:' . now()->subYears(18)->toDateString(), 'after:' . now()->subYears(80)->toDateString()],
            'mobileNumber' => ['required', 'string', 'regex:/^\+?[0-9 ()-]{10,17}$/', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:190', 'unique:users'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', Rule::in(Nigeria::STATES)],
            'nin' => ['required', 'digits:11'],
            'drivers_licence_number' => ['nullable', 'string', 'max:50'],
            'drivers_licence_expiry' => ['nullable', 'date'],
            'next_of_kin_name' => ['required', 'string', 'max:150'],
            'next_of_kin_phone' => ['required', 'string', 'regex:/^\+?[0-9 ()-]{10,17}$/'],
            'next_of_kin_relationship' => ['required', 'string', 'max:50'],

            // Health
            'weight' => ['nullable', 'numeric', 'between:30,250'],
            'height' => ['nullable', 'numeric', 'between:3,8'],
            'visual_impairment' => $health('visual_impairment'),
            'hearing_aid' => $health('hearing_aid'),
            'physical_disability' => $health('physical_disability'),
            'weed' => $health('weed'),
            'alcohol' => $health('alcohol'),
            'prescribed_medication' => ['nullable', 'string', 'max:2000'],
            'failed_drug_test' => ['nullable', 'string', 'max:2000'],
            'health_consent' => ['accepted'],

            // Documents & account
            'passport_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072', 'dimensions:min_width=200,min_height=200'],
            'drivers_licence_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
            'nin_slip_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
            'password' => $passwordRules,
            'terms' => ['accepted'],
        ];
    }

    public static function messages(): array
    {
        return [
            'selected_objective.required' => 'Please choose at least one course.',
            'selected_objective.*.exists' => 'One of the selected courses is no longer available.',
            'date_of_birth.before_or_equal' => 'You must be at least 18 years old to enrol.',
            'nin.digits' => 'Your NIN must be exactly 11 digits.',
            'mobileNumber.regex' => 'Enter a valid phone number, e.g. 0803 123 4567.',
            'mobileNumber.unique' => 'An account already uses this phone number. Try signing in instead.',
            'email.unique' => 'An account already uses this email address. Try signing in instead.',
            'next_of_kin_phone.regex' => 'Enter a valid phone number for your next of kin.',
            'health_consent.accepted' => 'Please confirm your health declaration and consent to us processing it.',
            'passport_photo.dimensions' => 'Your passport photograph must be at least 200 × 200 pixels.',
            'terms.accepted' => 'Please accept the terms and privacy policy.',
        ];
    }

    public static function attributes(): array
    {
        return [
            'firstName' => 'first name',
            'lastName' => 'last name',
            'middleName' => 'middle name',
            'mobileNumber' => 'phone number',
            'nin' => 'NIN',
            'physical_disability' => 'medical conditions',
            'weed' => 'drug use',
            'drivers_licence_file' => "driver's licence",
            'nin_slip_file' => 'NIN slip',
        ];
    }
}
