<?php

namespace App\Services;

use App\Models\EnrolmentBatch;
use App\Models\Role;
use App\Models\RoleCourse;
use App\Models\StudentInstructorDistribution;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Turns a paid application into an enrolment: marks the subscription paid,
 * activates the student and assigns an instructor for each course.
 */
class EnrolmentService
{
    public function completePayment(Subscription $subscription, string $reference, string $method): void
    {
        DB::transaction(function () use ($subscription, $reference, $method) {
            $subscription = Subscription::lockForUpdate()->findOrFail($subscription->id);

            if ($subscription->isPaid()) {
                return;
            }

            $subscription->update([
                'payment_status' => 'completed',
                'payment_reference' => $reference,
                'payment_method' => $method,
            ]);

            $student = $subscription->user;

            if (! $student->isSuspended()) {
                $student->update(['user_active' => true]);
            }

            $this->assignInstructors($student, $subscription->objectiveIds());
        });
    }

    /**
     * Assign the least-loaded active instructor whose role teaches each course.
     *
     * @return array<int, bool> course id => whether an instructor was assigned
     */
    public function assignInstructors(User $student, array $courseIds): array
    {
        $batchId = $this->ensureBatch($student);
        $results = [];

        foreach ($courseIds as $courseId) {
            $existing = StudentInstructorDistribution::where('student_id', $student->id)->where('course_id', $courseId)->exists();
            if ($existing) {
                $results[$courseId] = true;
                continue;
            }

            $instructor = $this->findInstructor($courseId);

            if (! $instructor) {
                Log::warning("No instructor available for course {$courseId}; student {$student->id} needs manual assignment.");
                $results[$courseId] = false;
                continue;
            }

            StudentInstructorDistribution::create([
                'enrolment_batch_id' => $batchId,
                'student_id' => $student->id,
                'instructor_id' => $instructor->id,
                'course_id' => $courseId,
            ]);

            $results[$courseId] = true;
        }

        return $results;
    }

    public function findInstructor(int $courseId): ?User
    {
        $instructorRoleIds = Role::where('portal', Role::PORTAL_INSTRUCTOR)->pluck('id');
        $courseRoleIds = RoleCourse::where('course_id', $courseId)->whereIn('role_id', $instructorRoleIds)->pluck('role_id');

        // Fall back to any instructor if no role is mapped to this course yet
        $eligibleRoleIds = $courseRoleIds->isNotEmpty() ? $courseRoleIds : $instructorRoleIds;

        return User::query()
            ->where('user_active', true)
            ->whereNull('suspended_at')
            ->whereHas('roles', fn ($q) => $q->whereIn('roles.id', $eligibleRoleIds))
            // Management accounts are never auto-assigned students
            ->whereDoesntHave('roles', fn ($q) => $q->where('portal', Role::PORTAL_ADMIN))
            ->withCount(['instructorDistributions as course_load' => fn ($q) => $q->where('course_id', $courseId)])
            ->orderBy('course_load')
            ->orderBy('id')
            ->first();
    }

    private function ensureBatch(User $student): ?int
    {
        if ($student->enrolment_batch_id) {
            return $student->enrolment_batch_id;
        }

        $batchId = EnrolmentBatch::where('active_batch', true)->value('id');
        if ($batchId) {
            $student->update(['enrolment_batch_id' => $batchId]);
        }

        return $batchId;
    }
}
