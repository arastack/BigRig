<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

/**
 * Messages sent through the website contact form, handled like an inbox.
 */
class EnquiryController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab') === 'all' ? 'all' : 'unread';
        $search = trim((string) $request->input('q'));

        $query = ContactMessage::query()->with('course')
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            }));

        $counts = [
            'unread' => (clone $query)->unread()->count(),
            'all' => (clone $query)->count(),
        ];

        $enquiries = $query
            ->when($tab === 'unread', fn ($q) => $q->unread())
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.enquiries.index', [
            'enquiries' => $enquiries,
            'tab' => $tab,
            'counts' => $counts,
            'search' => $search,
        ]);
    }

    public function show(Request $request, ContactMessage $enquiry)
    {
        // Opening an enquiry marks it as read by whoever opened it
        if (! $enquiry->read_at) {
            $enquiry->update(['read_at' => now(), 'handled_by' => $request->user()->id]);
        }

        $enquiry->load(['course', 'handler']);

        return view('admin.enquiries.show', ['enquiry' => $enquiry]);
    }

    public function update(Request $request, ContactMessage $enquiry)
    {
        if ($enquiry->read_at) {
            $enquiry->update(['read_at' => null]);

            return redirect()->route('admin.enquiries.index')->with('success', 'Marked as unread.');
        }

        $enquiry->update(['read_at' => now(), 'handled_by' => $request->user()->id]);

        return back()->with('success', 'Marked as read.');
    }

    public function destroy(ContactMessage $enquiry)
    {
        $enquiry->delete();

        return redirect()->route('admin.enquiries.index')->with('success', 'Enquiry deleted.');
    }
}
