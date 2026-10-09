<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TrainingObjective;
use Illuminate\Support\Arr;

class Subscription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'payment_reference',
        'objectives',
        'subtotal',
        'discount',
        'tax',
        'total_amount',
        'payment_status',
        'payment_method',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'objectives' => 'array', // Casts objectives as a JSON array
    ];

    /**
     * Relationship: Belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    /**
     * The selected course ids. The column holds JSON that older records encoded twice.
     */
    public function objectiveIds(): array
    {
        $ids = json_decode($this->attributes['objectives'] ?? '[]', true);

        if (is_string($ids)) {
            $ids = json_decode($ids, true);
        }

        return array_values(array_unique(array_map('intval', Arr::flatten((array) $ids))));
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'completed';
    }

    /**
     * Amount due in the smallest currency unit (kobo), as payment gateways expect.
     */
    public function amountInKobo(): int
    {
        return (int) round($this->total_amount * 100);
    }

    public function course()
    {
        return $this->belongsTo(TrainingObjective::class, 'course_id');
    }

    // Define the relationship method to get the objectives by decoding JSON
    // public function getObjectivesAttribute()
    // {
    //     // Decode the JSON stored in the 'objectives' column
    //     $objectiveIds = json_decode($this->attributes['objectives'], true);

    //     // If json_decode results in a stringified array, decode it again
    //     if (is_string($objectiveIds)) {
    //         $objectiveIds = json_decode($objectiveIds, true);
    //     }

    //     // Ensure that $objectiveIds is an array
    //     if (!is_array($objectiveIds)) {
    //         $objectiveIds = [];
    //     }

    //     // Return the related objectives from the training_objectives table
    //     return TrainingObjective::whereIn('id', $objectiveIds)->get();
    // }

    public function getObjectivesAttribute()
    {
        // Decode the JSON stored in the 'objectives' column
        $objectiveIds = json_decode($this->attributes['objectives'], true);

        // If decoding results in another JSON string, decode it again
        if (is_string($objectiveIds)) {
            $objectiveIds = json_decode($objectiveIds, true);
        }

        // Ensure $objectiveIds is a simple array and flatten it if necessary
        if (!is_array($objectiveIds)) {
            $objectiveIds = [];
        } else {
            $objectiveIds = Arr::flatten($objectiveIds); // Flattens any nested arrays
        }

        // Return the related objectives from the training_objectives table
        return TrainingObjective::whereIn('id', $objectiveIds)->get();
    }

}
