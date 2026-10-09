<?php

namespace App\Support;

use App\Models\StudentInstructorDistribution;
use App\Models\TrainingSchedule;
use App\Models\User;

/**
 * Where an applicant is in the admission journey, shown to applicants and staff.
 */
class ApplicationProgress
{
    public const SUBMITTED = 'submitted';
    public const AWAITING_PAYMENT = 'awaiting_payment';
    public const ENROLLED = 'enrolled';
    public const IN_TRAINING = 'in_training';
    public const SUSPENDED = 'suspended';

    public const LABELS = [
        self::AWAITING_PAYMENT => 'Awaiting payment',
        self::ENROLLED => 'Enrolled',
        self::IN_TRAINING => 'In training',
        self::SUSPENDED => 'Suspended',
    ];

    public function __construct(public readonly User $user)
    {
    }

    public static function for(User $user): self
    {
        return new self($user);
    }

    public function status(): string
    {
        if ($this->user->isSuspended()) {
            return self::SUSPENDED;
        }

        if (! $this->paidSubscription()) {
            return self::AWAITING_PAYMENT;
        }

        return $this->firstSession() && $this->firstSession()->schedule_date->isPast() ? self::IN_TRAINING : self::ENROLLED;
    }

    public function label(): string
    {
        return self::LABELS[$this->status()];
    }

    /**
     * Timeline steps, each with a state of done, current or upcoming.
     */
    public function steps(): array
    {
        $paid = $this->paidSubscription();
        $assigned = $this->instructorsAssigned();
        $session = $this->firstSession();
        $hasPhoto = filled($this->user->profile_photo_path);

        $steps = [
            [
                'title' => 'Application submitted',
                'detail' => 'Received ' . $this->user->created_at?->format('j M Y'),
                'done' => true,
            ],
            [
                'title' => 'Documents',
                'detail' => $hasPhoto ? 'Passport photograph received' . ($this->user->documents->isNotEmpty() ? ' · ' . $this->user->documents->count() . ' other document(s)' : '') : 'Upload your passport photograph',
                'done' => $hasPhoto,
            ],
            [
                'title' => 'Fees paid',
                'detail' => $paid ? 'Paid ' . $paid->updated_at->format('j M Y') . ' · Ref ' . $paid->payment_reference : 'Pay to confirm your place',
                'done' => (bool) $paid,
            ],
            [
                'title' => 'Instructor assigned',
                'detail' => $assigned ? 'Your instructor(s) are in your student portal' : 'Assigned automatically once fees are paid',
                'done' => $assigned,
            ],
            [
                'title' => 'Training begins',
                'detail' => $session ? 'First session ' . $session->schedule_date->format('D j M Y') : 'Your timetable appears once sessions are scheduled',
                'done' => $session && $session->schedule_date->isPast(),
            ],
        ];

        // The first step that isn't done is the current one
        $current = collect($steps)->search(fn ($step) => ! $step['done']);

        return collect($steps)->map(fn ($step, $i) => $step + [
            'state' => $step['done'] ? 'done' : ($i === $current ? 'current' : 'upcoming'),
        ])->all();
    }

    public function paidSubscription()
    {
        return once(fn () => $this->user->subscriptions()->where('payment_status', 'completed')->latest()->first());
    }

    public function pendingSubscription()
    {
        return once(fn () => $this->user->subscriptions()->whereIn('payment_status', ['pending', 'failed'])->latest()->first());
    }

    private function instructorsAssigned(): bool
    {
        return once(fn () => StudentInstructorDistribution::where('student_id', $this->user->id)->exists());
    }

    private function firstSession(): ?TrainingSchedule
    {
        return once(function () {
            $courseIds = StudentInstructorDistribution::where('student_id', $this->user->id)->pluck('course_id');

            return TrainingSchedule::whereIn('course_id', $courseIds)
                ->when($this->user->enrolment_batch_id, fn ($q) => $q->where('batch_id', $this->user->enrolment_batch_id))
                ->orderBy('schedule_date')
                ->get()
                ->first(fn ($session) => $session->hasStudent($this->user->id));
        });
    }
}
