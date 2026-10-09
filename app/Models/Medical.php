<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medical extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'weight',
        'height',
        'visual_impairment',
        'hearing_aid',
        'physical_disability',
        'weed',
        'alcohol',
        'prescribed_medication',
        'failed_drug_test',
        'attachments',
        'consent_given_at',
    ];

    protected $casts = [
        'consent_given_at' => 'datetime',
    ];

    /**
     * Relationship: Belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
