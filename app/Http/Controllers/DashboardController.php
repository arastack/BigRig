<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\EnrolmentBatch;
use App\Models\Role;
use App\Models\ScheduleChangeRequest;
use App\Models\Subscription;
use App\Models\TrainingObjective;
use App\Models\TrainingSchedule;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isStudent()) {
            return redirect()->route('student.dashboard');
        }

        if ($user->portal() !== Role::PORTAL_ADMIN) {
            if ($user->isInstructor()) {
                return redirect()->route('instructor.dashboard');
            }

            abort(403, 'Your account has no role assigned yet. Please contact the school.');
        }

        $students = User::whereHas('roles', fn ($q) => $q->where('roles.id', Role::STUDENT_ROLE_ID));
        $paid = Subscription::where('payment_status', 'completed');

        $applicants = (clone $students)->whereNull('suspended_at')->whereDoesntHave('subscriptions', fn ($q) => $q->where('payment_status', 'completed'));
        $enrolled = (clone $students)->whereNull('suspended_at')->whereHas('subscriptions', fn ($q) => $q->where('payment_status', 'completed'));
        $totalApplications = (clone $students)->count();
        $enrolledCount = (clone $enrolled)->count();

        $metrics = [
            'applicants' => (clone $applicants)->count(),
            'applicantsThisWeek' => (clone $applicants)->where('created_at', '>=', now()->subWeek())->count(),
            'enrolled' => $enrolledCount,
            'conversion' => $totalApplications ? round($enrolledCount / $totalApplications * 100) : 0,
            'revenue' => (float) (clone $paid)->sum('total_amount'),
            'revenueThisMonth' => (float) (clone $paid)->where('updated_at', '>=', now()->startOfMonth())->sum('total_amount'),
            'outstanding' => (float) Subscription::whereIn('payment_status', ['pending', 'failed'])
                ->whereHas('user', fn ($q) => $q->whereNull('suspended_at'))->sum('total_amount'),
            'staff' => User::whereNull('suspended_at')->whereHas('roles', fn ($q) => $q->where('portal', '!=', Role::PORTAL_STUDENT))->count(),
            'instructors' => User::whereNull('suspended_at')->whereHas('roles', fn ($q) => $q->where('portal', Role::PORTAL_INSTRUCTOR))->count(),
            'sessionsToday' => TrainingSchedule::whereDate('schedule_date', today())->count(),
            'unreadEnquiries' => ContactMessage::unread()->count(),
            'pendingChanges' => ScheduleChangeRequest::where('status', 'pending')->count(),
        ];

        // Applications vs. enrolments over the last 12 months
        $months = collect(CarbonPeriod::create(now()->subMonths(11)->startOfMonth(), '1 month', now()->startOfMonth()))
            ->map(fn ($month) => $month->format('Y-m'));
        $applied = (clone $students)->where('created_at', '>=', now()->subMonths(11)->startOfMonth())->get(['created_at'])
            ->countBy(fn ($u) => $u->created_at->format('Y-m'));
        $paidByMonth = (clone $paid)->where('updated_at', '>=', now()->subMonths(11)->startOfMonth())->get(['updated_at', 'total_amount'])
            ->groupBy(fn ($s) => $s->updated_at->format('Y-m'));

        $trend = [
            'labels' => $months->map(fn ($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->format('M y'))->values(),
            'applications' => $months->map(fn ($m) => $applied[$m] ?? 0)->values(),
            'enrolments' => $months->map(fn ($m) => isset($paidByMonth[$m]) ? $paidByMonth[$m]->count() : 0)->values(),
            'revenue' => $months->map(fn ($m) => isset($paidByMonth[$m]) ? round($paidByMonth[$m]->sum('total_amount')) : 0)->values(),
        ];

        // Demand by course across all applications
        $courseNames = TrainingObjective::pluck('objective', 'id');
        $demand = Subscription::get()
            ->groupBy('user_id')->map(fn ($subs) => $subs->sortByDesc('id')->first())
            ->flatMap(fn ($s) => collect($s->objectiveIds())->map(fn ($id) => ['id' => $id, 'paid' => $s->isPaid()]))
            ->groupBy('id')
            ->map(fn ($rows, $id) => ['course' => $courseNames[$id] ?? "Course {$id}", 'applied' => $rows->count(), 'paid' => $rows->where('paid', true)->count()])
            ->sortByDesc('applied')
            ->values();

        return view('admin.dashboard', [
            'user' => $user,
            'metrics' => $metrics,
            'trend' => $trend,
            'demand' => $demand,
            'activeBatch' => EnrolmentBatch::where('active_batch', true)->first(),
            'recentApplicants' => (clone $students)->with('subscriptions')->latest()->take(6)->get(),
            'recentPayments' => (clone $paid)->with('user')->latest('updated_at')->take(6)->get(),
            'upcoming' => TrainingSchedule::with(['course', 'instructor', 'topic'])
                ->whereBetween('schedule_date', [today(), today()->addDays(7)])
                ->orderBy('schedule_date')->orderBy('time_start')->take(8)->get(),
        ]);
    }
}
