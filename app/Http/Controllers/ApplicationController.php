<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\CreateNewUser;
use App\Models\ApplicantDocument;
use App\Models\TrainingObjective;
use App\Models\User;
use App\Support\ApplicationProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * The applicant's own view of their application: progress, courses, documents and payment.
 */
class ApplicationController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('documents', 'medical');
        $progress = ApplicationProgress::for($user);
        $subscription = $progress->pendingSubscription() ?? $progress->paidSubscription();

        return view('application.show', [
            'user' => $user,
            'progress' => $progress,
            'subscription' => $subscription,
            'courses' => TrainingObjective::orderBy('price')->get(),
        ]);
    }

    /**
     * Change the selected courses while the application is still unpaid.
     */
    public function updateCourses(Request $request)
    {
        $data = $request->validate([
            'selected_objective' => ['required', 'array', 'min:1'],
            'selected_objective.*' => ['integer', 'exists:training_objectives,id'],
        ], ['selected_objective.required' => 'Please choose at least one course.']);

        $user = $request->user();
        $pending = ApplicationProgress::for($user)->pendingSubscription();

        if (! $pending) {
            return back()->with('error', 'Your courses can no longer be changed online. Please contact admissions.');
        }

        $replacement = CreateNewUser::createSubscription($user, $data['selected_objective']);
        $pending->delete();

        return back()->with('success', 'Your course selection has been updated. New total: ₦' . number_format($replacement->total_amount, 2));
    }

    public function uploadDocument(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(array_merge(['passport_photo'], array_keys(ApplicantDocument::TYPES)))],
            'file' => ['required', 'file', 'max:5120', $request->input('type') === 'passport_photo' ? 'mimes:jpg,jpeg,png,webp' : 'mimes:pdf,jpg,jpeg,png,webp'],
        ]);

        $user = $request->user();

        if ($data['type'] === 'passport_photo') {
            $old = $user->profile_photo_path;
            $user->forceFill(['profile_photo_path' => $request->file('file')->store('profile-photos', 'public')])->save();
            if ($old) {
                Storage::disk('public')->delete($old);
            }
        } else {
            ApplicantDocument::storeFor($user, $data['type'], $request->file('file'));
        }

        return back()->with('success', 'Document uploaded.');
    }

    /**
     * Applicants download their own documents; staff with admissions access download anyone's.
     */
    public function downloadDocument(Request $request, ApplicantDocument $document)
    {
        $user = $request->user();

        abort_unless($document->user_id === $user->id || $user->hasPermission('read_applicants'), 403);

        return Storage::disk(ApplicantDocument::DISK)->response($document->path, $document->original_name);
    }

    public function destroyDocument(Request $request, ApplicantDocument $document)
    {
        abort_unless($document->user_id === $request->user()->id && $document->status !== 'approved', 403);

        $document->deleteWithFile();

        return back()->with('success', 'Document removed.');
    }
}
