<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Subscription;
use App\Models\TrainingObjective;
use App\Models\TrainingSchedule;
use App\Models\Curriculum;
use App\Models\Instructors;
use App\Models\Subscriptions;

class DashboardController extends Controller
{

    public function index()
    {
        $user = Auth::user();

        if ($user->isStudent()) {
            return redirect()->route('student.dashboard');
        }

        if ($user->portal() !== 'admin') {
            if ($user->isInstructor()) {
                return redirect()->route('instructor.dashboard');
            }

            abort(403, 'Your account has no role assigned yet. Please contact the school.');
        }

        // Get the number of active users
        $activeStudents = User::where('user_active', 1)
        ->whereHas('roles', function ($query) {
            $query->where('roles.id', 10);
        })
        ->count();

        // Get the number of active users
        $instructors = User::where('user_active', 1)
        ->whereHas('roles', function ($query) {
            $query->where('roles.id', '!=', 10);
        })
        ->count();

        // Get the number of users created today
        $today = Carbon::today();
        $studentsToday = User::whereDate('updated_at', $today)->count();

        // Get the number of users created yesterday
        $yesterday = Carbon::yesterday();
        $studentsYesterday = User::whereDate('updated_at', $yesterday)->count();

        // Calculate the percentage increase between yesterday and today for users
        $studentPI = 0;
        if ($studentsYesterday > 0) {
            $studentPI = (($studentsToday - $studentsYesterday) / $studentsYesterday) * 100;
        }

        // Get all revenue
        $revenue = Subscription::where('payment_status', 'completed')
            ->sum('total_amount');

        // Sum of total_amount for subscriptions where payment_status is completed today
        $revenueToday = Subscription::where('payment_status', 'completed')
            ->whereDate('updated_at', $today)
            ->sum('total_amount');

        // Sum of total_amount for subscriptions where payment_status is completed yesterday
        $revenueYesterday = Subscription::where('payment_status', 'completed')
            ->whereDate('updated_at', $yesterday)
            ->sum('total_amount');

        // Calculate the percentage increase in total_amount between yesterday and today
        $revenuePI = 0;
        if ($revenueYesterday > 0) {
            $revenuePI = (($revenueToday - $revenueYesterday) / $revenueYesterday) * 100;
        }

        $schedules = TrainingSchedule::with(['instructor', 'course', 'topic'])->orderBy('schedule_date', 'asc')->paginate(10);

        $subscriptions = Subscription::with('user')
            ->when($user->roles->contains('id', 10), function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('payment_status', 'completed')
            ->take(10)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('pages/dashboard/dashboard', compact('instructors', 'activeStudents', 'studentPI', 'revenue', 'revenuePI', 'schedules', 'subscriptions'));
    }

    /**
     * Displays the analytics screen
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    // public function analytics()
    // {
    //     return view('pages/dashboard/analytics');
    // }

    /**
     * Displays the fintech screen
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    // public function fintech()
    // {
    //     return view('pages/dashboard/fintech');
    // }
}
