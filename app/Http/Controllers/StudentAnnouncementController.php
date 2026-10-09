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

        $announcements = Announcement::visibleTo($user)
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
        $announcement = Announcement::visibleTo($user)->findOrFail($id);

        // Mark as read
        if (!$announcement->isReadBy($user->id)) {
            $announcement->markAsReadBy($user->id);
        }

        return view('student.announcement-detail', compact('announcement'));
    }
}
