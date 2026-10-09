<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\EnrolmentBatch;
use App\Models\Role;
use App\Models\StudentInstructorDistribution;
use App\Models\TrainingSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Intake batches. Exactly one batch is open at a time; new applicants join it.
 */
class BatchController extends Controller
{
    public function index()
    {
        $batches = EnrolmentBatch::orderByDesc('active_batch')->orderByDesc('c_date')->orderByDesc('id')->get();

        $students = fn () => User::query()
            ->whereHas('roles', fn ($q) => $q->where('roles.id', Role::STUDENT_ROLE_ID))
            ->whereNotNull('enrolment_batch_id');

        $enrolled = $students()
            ->whereHas('subscriptions', fn ($q) => $q->where('payment_status', 'completed'))
            ->selectRaw('enrolment_batch_id, COUNT(*) as total')
            ->groupBy('enrolment_batch_id')
            ->pluck('total', 'enrolment_batch_id');

        $applicants = $students()
            ->whereDoesntHave('subscriptions', fn ($q) => $q->where('payment_status', 'completed'))
            ->selectRaw('enrolment_batch_id, COUNT(*) as total')
            ->groupBy('enrolment_batch_id')
            ->pluck('total', 'enrolment_batch_id');

        return view('admin.batches.index', [
            'batches' => $batches,
            'openBatch' => $batches->firstWhere('active_batch', true),
            'enrolledCounts' => $enrolled,
            'applicantCounts' => $applicants,
            'blockers' => $batches->mapWithKeys(fn ($batch) => [$batch->id => $this->deleteBlocker($batch)]),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $batch = DB::transaction(function () use ($data, $request) {
            $batch = EnrolmentBatch::create([
                'batch_name' => $data['batch_name'],
                'c_date' => $data['c_date'],
                'active_batch' => false,
            ]);

            if ($request->boolean('activate')) {
                $this->makeActive($batch);
            }

            return $batch;
        });

        return back()->with('success', $batch->active_batch
            ? "{$batch->batch_name} created and opened. New applicants will join this batch."
            : "{$batch->batch_name} created. Open it when you are ready to take applications for it.");
    }

    public function update(Request $request, EnrolmentBatch $batch)
    {
        $batch->update($this->validated($request, $batch));

        return back()->with('success', "{$batch->batch_name} updated.");
    }

    /**
     * Open this batch for applications and close every other batch.
     */
    public function activate(EnrolmentBatch $batch)
    {
        DB::transaction(fn () => $this->makeActive($batch));

        return back()->with('success', "{$batch->batch_name} is now open. New applicants will join this batch.");
    }

    public function destroy(EnrolmentBatch $batch)
    {
        if ($reason = $this->deleteBlocker($batch)) {
            return back()->with('error', "{$batch->batch_name} can't be deleted: {$reason}");
        }

        $name = $batch->batch_name;
        $wasOpen = $batch->active_batch;
        $batch->delete();

        return back()->with('success', $wasOpen
            ? "{$name} deleted. No batch is open now, so the website shows admissions as closed."
            : "{$name} deleted.");
    }

    private function makeActive(EnrolmentBatch $batch): void
    {
        EnrolmentBatch::whereKeyNot($batch->id)->where('active_batch', true)->update(['active_batch' => false]);
        $batch->forceFill(['active_batch' => true])->save();
    }

    private function validated(Request $request, ?EnrolmentBatch $batch = null): array
    {
        return $request->validate([
            'batch_name' => ['required', 'string', 'max:255', 'unique:enrolment_batches,batch_name' . ($batch ? ',' . $batch->id : '')],
            'c_date' => ['required', 'date'],
        ], [
            'batch_name.unique' => 'Another batch already has this name.',
        ], [
            'batch_name' => 'batch name',
            'c_date' => 'start date',
        ]);
    }

    /**
     * Why a batch can't be deleted, or null when it is safe to remove.
     */
    private function deleteBlocker(EnrolmentBatch $batch): ?string
    {
        $reasons = array_filter([
            ($n = User::where('enrolment_batch_id', $batch->id)->count()) ? $n . ' ' . str('person')->plural($n) : null,
            ($n = TrainingSchedule::where('batch_id', $batch->id)->count()) ? $n . ' training ' . str('session')->plural($n) : null,
            ($n = StudentInstructorDistribution::where('enrolment_batch_id', $batch->id)->count()) ? $n . ' instructor ' . str('assignment')->plural($n) : null,
            // Announcements are deleted with their batch by the database, so keep them safe too
            ($n = Announcement::where('batch_id', $batch->id)->count()) ? $n . ' ' . str('announcement')->plural($n) : null,
        ]);

        return $reasons ? 'it is still used by ' . implode(', ', $reasons) . '. Move them to another batch first.' : null;
    }
}
