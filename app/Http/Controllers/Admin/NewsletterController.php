<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailSubscription;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * People who signed up for the newsletter on the website.
 */
class NewsletterController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('q'));

        return view('admin.newsletter.index', [
            'subscribers' => $this->filtered($search)->latest()->paginate(50)->withQueryString(),
            'total' => EmailSubscription::count(),
            'thisMonth' => EmailSubscription::where('created_at', '>=', now()->startOfMonth())->count(),
            'search' => $search,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $rows = $this->filtered(trim((string) $request->input('q')))->latest()->get(['email', 'created_at']);

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Email', 'Subscribed on']);

            foreach ($rows as $subscriber) {
                fputcsv($out, [$subscriber->email, $subscriber->created_at?->format('Y-m-d H:i')]);
            }

            fclose($out);
        }, 'bigrig-newsletter-subscribers-' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv']);
    }

    public function destroy(EmailSubscription $subscriber)
    {
        $subscriber->delete();

        return back()->with('success', "{$subscriber->email} has been removed from the newsletter list.");
    }

    private function filtered(string $search): Builder
    {
        return EmailSubscription::query()->when($search, fn ($q) => $q->where('email', 'like', "%{$search}%"));
    }
}
