<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'course_id', 'subject', 'message', 'ip_address', 'read_at', 'handled_by'];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(TrainingObjective::class, 'course_id');
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
}
