<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Medical;
use App\Models\Subscription;
use App\Models\TrainingObjective;
use App\Models\EnrolmentBatch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string|array>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'firstName' => ['required', 'string', 'max:255'],
            'middleName' => ['nullable', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string', 'in:Male,Female'],
            'mobileNumber' => ['required', 'string', 'max:20', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'weight' => ['nullable', 'numeric'],
            'height' => ['nullable', 'numeric'],
            'visual_impairment' => ['required', 'string', 'in:None,Long Sightedness,Short Sightedness,Color Blindness'],
            'hearing_aid' => ['required', 'string', 'in:None,BTE,ITE,RITE,ITC,CROS'],
            'physical_disability' => ['required', 'string', 'in:None,Ulcer,Cancer,HIV,Abdominal Pain'],
            'weed' => ['required', 'string', 'in:Yes,No'],
            'alcohol' => ['required', 'string', 'in:No,Often,Casually,Daily User'],
            'prescribed_medication' => ['nullable', 'string'],
            'failed_drug_test' => ['nullable', 'string'],
            'selected_objective' => ['required', 'array', 'min:1'],
            'selected_objective.*' => ['integer', 'exists:training_objectives,id'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ], [
            'selected_objective.required' => 'Please select at least one training objective.',
            'selected_objective.*.exists' => 'One of the selected training objectives is no longer available.',
        ])->validate();

        $activeBatch = EnrolmentBatch::where('active_batch', true)->first();

        if (! $activeBatch) {
            throw ValidationException::withMessages([
                'selected_objective' => 'Enrolment is currently closed. Please check back later or contact us.',
            ]);
        }

        // Create the user and all related records atomically, so a failure part-way
        // does not leave an orphaned account that blocks the applicant from retrying.
        return DB::transaction(function () use ($input, $activeBatch) {
            // Create the user (without role_id)
            $user = User::create([
                'enrolment_batch_id' => $activeBatch->id,
                'firstName' => $input['firstName'],
                'middleName' => $input['middleName'] ?? null,
                'lastName' => $input['lastName'],
                'gender' => $input['gender'],
                'mobileNumber' => $input['mobileNumber'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'profile_photo_path' => null,
                'user_active' => 0,
            ]);

            // Attach default role via pivot table
            $user->roles()->attach(10);

            // Store medical details
            Medical::create([
                'user_id' => $user->id,
                'weight' => $input['weight'] ?? null,
                'height' => $input['height'] ?? null,
                'visual_impairment' => $input['visual_impairment'],
                'hearing_aid' => $input['hearing_aid'],
                'physical_disability' => $input['physical_disability'],
                'weed' => $input['weed'],
                'alcohol' => $input['alcohol'],
                'prescribed_medication' => $input['prescribed_medication'] ?? null,
                'failed_drug_test' => $input['failed_drug_test'] ?? null,
                'attachments' => $input['attachments'] ?? null,
            ]);

            // Fetch the selected objectives from the 'selected_objective' array
            $selectedObjectives = TrainingObjective::whereIn('id', $input['selected_objective'])->get();


            // Calculate the subtotal (sum of prices of selected objectives)
            $subtotal = $selectedObjectives->sum(function($objective) {
                return $objective->price;
            });

            // Assume a tax rate of 7.5%
            $taxRate = 0.075;

            // Calculate the tax amount based on the subtotal
            $tax = $subtotal * $taxRate;

            // Calculate the total amount (subtotal + tax)
            $totalAmount = $subtotal + $tax;

            // Store subscription details
            Subscription::create([
                'user_id' => $user->id,
                'payment_reference' => null,
                'objectives' => json_encode(array_map('intval', $input['selected_objective'])),
                'subtotal' => $subtotal, // Store the subtotal
                'tax' => $tax,           // Store the tax amount
                'total_amount' => $totalAmount, // Store the total amount (subtotal + tax)
                'payment_status' => 'pending',
                'payment_method' => null,
            ]);

            return $user;
        });
    }
}
