<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One saved admissions setting (health questions, relationships, attendance threshold).
 * Read through App\Support\AdmissionSettings, which supplies defaults.
 */
class AdmissionSetting extends Model
{
    protected $fillable = ['key', 'value'];

    protected $casts = ['value' => 'array'];
}
