<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'type',
        'priority',
        'course_id',
        'batch_id',
        'created_by',
        'is_active',
        'publish_date',
        'expiry_date'
    ];

    protected $casts = [
        'publish_date' => 'datetime',
        'expiry_date' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(TrainingObjective::class, 'course_id');
    }

    public function batch()
    {
        return $this->belongsTo(EnrolmentBatch::class, 'batch_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Active, published announcements addressed to a student's batch and paid courses.
     */
    public function scopeVisibleTo($query, User $student)
    {
        $courseIds = Subscription::where('user_id', $student->id)
            ->where('payment_status', 'completed')
            ->get()
            ->flatMap(fn ($subscription) => $subscription->objectiveIds())
            ->unique()
            ->all();

        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('batch_id')->orWhere('batch_id', $student->enrolment_batch_id))
            ->where(fn ($q) => $q->whereNull('course_id')->orWhereIn('course_id', $courseIds))
            ->where(fn ($q) => $q->whereNull('publish_date')->orWhere('publish_date', '<=', now()))
            ->where(fn ($q) => $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now()));
    }

    public function reads()
    {
        return $this->hasMany(AnnouncementRead::class);
    }

    public function isReadBy($userId)
    {
        return $this->reads()->where('user_id', $userId)->exists();
    }

    public function markAsReadBy($userId)
    {
        return AnnouncementRead::firstOrCreate([
            'announcement_id' => $this->id,
            'user_id' => $userId,
        ], [
            'read_at' => now()
        ]);
    }
}
