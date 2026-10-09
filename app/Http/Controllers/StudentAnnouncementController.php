<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcement;
use App\Models\Subscription;

class StudentAnnouncementController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $announcements = $this->visibleTo($user)
            ->with(['course', 'batch', 'creator'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Mark announcements as read
        foreach ($announcements as $announcement) {
            if (!$announcement->isReadBy($user->id)) {
                $announcement->markAsReadBy($user->id);
            }
            $announcement->is_read = true; // For display purposes
        }

        return view('student.announcements', compact('announcements'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $announcement = $this->visibleTo($user)->findOrFail($id);

        // Mark as read
        if (!$announcement->isReadBy($user->id)) {
            $announcement->markAsReadBy($user->id);
        }

        return view('student.announcement-detail', compact('announcement'));
    }

    /**
     * Active, published announcements addressed to this student's batch and courses.
     */
    private function visibleTo($user)
    {
        $enrolledCourseIds = Subscription::where('user_id', $user->id)
            ->where('payment_status', 'completed')
            ->get()
            ->flatMap(fn ($subscription) => $subscription->objectiveIds())
            ->unique()
            ->all();

        return Announcement::where('is_active', true)
            ->where(fn ($q) => $q->whereNull('batch_id')->orWhere('batch_id', $user->enrolment_batch_id))
            ->where(fn ($q) => $q->whereNull('course_id')->orWhereIn('course_id', $enrolledCourseIds))
            ->where(fn ($q) => $q->whereNull('publish_date')->orWhere('publish_date', '<=', now()))
            ->where(fn ($q) => $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now()));
    }
}
