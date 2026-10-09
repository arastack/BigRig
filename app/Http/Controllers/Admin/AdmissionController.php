<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Controllers\Controller;
use App\Models\ApplicantDocument;
use App\Models\EnrolmentBatch;
use App\Models\Role;
use App\Models\StudentAttendance;
use App\Models\StudentInstructorDistribution;
use App\Models\Subscription;
use App\Models\TrainingObjective;
use App\Models\User;
use App\Services\EnrolmentService;
use App\Support\ApplicationProgress;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Applicants and enrolled students: one list with status tabs, and a full record per person.
 */
class AdmissionController extends Controller
{
    public const STATUSES = [
        'awaiting_payment' => 'Applicants',
        'enrolled' => 'Enrolled',
        'suspended' => 'Suspended',
        'all' => 'Everyone',
    ];

    public function index(Request $request)
    {
        $user = $request->user();
        $status = $this->allowedStatus($request);

        $students = $this->filtered($request, $status)
            ->with(['subscriptions' => fn ($q) => $q->latest(), 'enrolmentBatch'])
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $counts = collect(self::STATUSES)->keys()
            ->mapWithKeys(fn ($key) => [$key => $this->filtered($request, $key, withStatusOnly: true)->count()]);

        return view('admin.admissions.index', [
            'students' => $students,
            'status' => $status,
            'counts' => $counts,
            'statuses' => collect(self::STATUSES)->filter(fn ($label, $key) => $key !== 'awaiting_payment' || $user->hasPermission('read_applicants')),
            'courses' => TrainingObjective::orderBy('objective')->get(),
            'batches' => EnrolmentBatch::orderByDesc('id')->get(),
            'filters' => $request->only(['q', 'course', 'batch']),
        ]);
    }

    public function show(User $student)
    {
        abort_unless($student->isStudent(), 404);

        $student->load(['medical', 'documents.reviewer', 'enrolmentBatch', 'subscriptions' => fn ($q) => $q->latest()]);

        $distributions = StudentInstructorDistribution::with(['instructor', 'course'])
            ->where('student_id', $student->id)
            ->get();

        $attendance = StudentAttendance::where('student_id', $student->id)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $eligibleInstructors = User::with('roles')
            ->whereNull('suspended_at')
            ->where('user_active', true)
            ->whereHas('roles', fn ($q) => $q->where('portal', Role::PORTAL_INSTRUCTOR))
            ->orderBy('firstName')
            ->get();

        return view('admin.admissions.show', [
            'student' => $student,
            'progress' => ApplicationProgress::for($student),
            'distributions' => $distributions,
            'attendance' => $attendance,
            'courses' => TrainingObjective::orderBy('objective')->get(),
            'batches' => EnrolmentBatch::orderByDesc('id')->get(),
            'instructors' => $eligibleInstructors,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $status = $this->allowedStatus($request);
        $rows = $this->filtered($request, $status)->with(['subscriptions' => fn ($q) => $q->latest(), 'enrolmentBatch'])->latest()->get();
        $courseNames = TrainingObjective::pluck('objective', 'id');

        $filename = 'bigrig-' . Str::slug(self::STATUSES[$status]) . '-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($rows, $courseNames) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'First name', 'Middle name', 'Last name', 'Gender', 'Date of birth', 'Email', 'Phone', 'Address', 'City', 'State', 'NIN', 'Licence no.', 'Next of kin', 'Next of kin phone', 'Batch', 'Courses', 'Total (NGN)', 'Payment status', 'Payment ref', 'Applied on', 'Status']);

            foreach ($rows as $student) {
                $subscription = $student->subscriptions->firstWhere('payment_status', 'completed') ?? $student->subscriptions->first();
                fputcsv($out, [
                    $student->id, $student->firstName, $student->middleName, $student->lastName, $student->gender,
                    $student->date_of_birth?->format('Y-m-d'), $student->email, $student->mobileNumber,
                    $student->address, $student->city, $student->state, $student->nin, $student->drivers_licence_number,
                    $student->next_of_kin_name, $student->next_of_kin_phone, $student->enrolmentBatch?->batch_name,
                    collect($subscription?->objectiveIds() ?? [])->map(fn ($id) => $courseNames[$id] ?? $id)->join('; '),
                    $subscription?->total_amount, $subscription?->payment_status, $subscription?->payment_reference,
                    $student->created_at?->format('Y-m-d'), ApplicationProgress::for($student)->label(),
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * Record a payment made at the school, by transfer or POS, and enrol the student.
     */
    public function recordPayment(Request $request, User $student, EnrolmentService $enrolment)
    {
        $data = $request->validate([
            'method' => ['required', Rule::in(['Bank transfer', 'Cash', 'POS', 'Paystack (manual)'])],
            'reference' => ['required', 'string', 'max:100', Rule::unique('subscriptions', 'payment_reference')],
            'confirm_amount' => ['accepted'],
        ], ['confirm_amount.accepted' => 'Confirm that the full amount due has been received.']);

        $subscription = ApplicationProgress::for($student)->pendingSubscription();

        if (! $subscription) {
            return back()->with('error', 'This student has no unpaid application.');
        }

        $enrolment->completePayment($subscription, $data['reference'], $data['method'] . ' · recorded by ' . $request->user()->firstName);

        return back()->with('success', "Payment recorded. {$student->firstName} is now enrolled and instructors have been assigned.");
    }

    public function updateCourses(Request $request, User $student)
    {
        $data = $request->validate([
            'selected_objective' => ['required', 'array', 'min:1'],
            'selected_objective.*' => ['integer', 'exists:training_objectives,id'],
        ]);

        $pending = ApplicationProgress::for($student)->pendingSubscription();

        if (! $pending) {
            return back()->with('error', 'Courses can only be changed before payment.');
        }

        CreateNewUser::createSubscription($student, $data['selected_objective']);
        $pending->delete();

        return back()->with('success', 'Course selection updated and fees recalculated.');
    }

    public function updateBatch(Request $request, User $student)
    {
        $data = $request->validate(['enrolment_batch_id' => ['required', 'exists:enrolment_batches,id']]);

        $student->update($data);
        StudentInstructorDistribution::where('student_id', $student->id)->update(['enrolment_batch_id' => $data['enrolment_batch_id']]);

        return back()->with('success', 'Intake batch updated.');
    }

    public function assignInstructor(Request $request, User $student)
    {
        $data = $request->validate([
            'course_id' => ['required', 'exists:training_objectives,id'],
            'instructor_id' => ['required', Rule::exists('users', 'id')],
        ]);

        $instructor = User::findOrFail($data['instructor_id']);
        abort_unless($instructor->isInstructor(), 422, 'Choose an instructor account.');

        StudentInstructorDistribution::updateOrCreate(
            ['student_id' => $student->id, 'course_id' => $data['course_id']],
            ['instructor_id' => $instructor->id, 'enrolment_batch_id' => $student->enrolment_batch_id]
        );

        return back()->with('success', "{$instructor->firstName} {$instructor->lastName} is now the instructor for this course.");
    }

    public function reviewDocument(Request $request, User $student, ApplicantDocument $document)
    {
        abort_unless($document->user_id === $student->id, 404);

        $data = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
            'review_note' => ['nullable', 'required_if:status,rejected', 'string', 'max:255'],
        ], ['review_note.required_if' => 'Tell the applicant why the document was rejected.']);

        $document->update($data + ['reviewed_by' => $request->user()->id, 'reviewed_at' => now()]);

        return back()->with('success', $document->label . ' ' . $data['status'] . '.');
    }

    /**
     * The applicant/student query for a status tab plus the search filters.
     */
    private function filtered(Request $request, string $status, bool $withStatusOnly = false): Builder
    {
        $query = User::query()->whereHas('roles', fn ($q) => $q->where('roles.id', Role::STUDENT_ROLE_ID));

        match ($status) {
            'awaiting_payment' => $query->whereNull('suspended_at')->whereDoesntHave('subscriptions', fn ($q) => $q->where('payment_status', 'completed')),
            'enrolled' => $query->whereNull('suspended_at')->whereHas('subscriptions', fn ($q) => $q->where('payment_status', 'completed')),
            'suspended' => $query->whereNotNull('suspended_at'),
            default => null,
        };

        if ($withStatusOnly) {
            return $query;
        }

        if ($search = trim((string) $request->input('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('firstName', 'like', "%{$search}%")
                    ->orWhere('lastName', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobileNumber', 'like', "%{$search}%")
                    ->orWhere('nin', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(firstName, ' ', lastName) LIKE ?", ["%{$search}%"]);
                if (ctype_digit(ltrim($search, '#0'))) {
                    $q->orWhere('id', (int) ltrim($search, '#0'));
                }
            });
        }

        if ($batch = $request->integer('batch')) {
            $query->where('enrolment_batch_id', $batch);
        }

        if ($course = $request->integer('course')) {
            // Courses live in JSON on the subscription; match the id as a whole number
            $query->whereHas('subscriptions', fn ($q) => $q->where(function ($q) use ($course) {
                foreach (['[%d]', '[%d,%%', '%%,%d,%%', '%%,%d]'] as $pattern) {
                    $q->orWhere('objectives', 'like', sprintf($pattern, $course));
                }
            }));
        }

        return $query;
    }

    private function allowedStatus(Request $request): string
    {
        $status = $request->input('status', 'awaiting_payment');
        $status = array_key_exists($status, self::STATUSES) ? $status : 'awaiting_payment';

        // Staff who can only see enrolled students never land on the applicants tab
        if ($status === 'awaiting_payment' && ! $request->user()->hasPermission('read_applicants')) {
            return 'enrolled';
        }

        return $status;
    }
}
