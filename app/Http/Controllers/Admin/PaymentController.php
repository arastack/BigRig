<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\TrainingObjective;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Every fee record (paid, pending and failed) with filters, totals and a CSV export.
 */
class PaymentController extends Controller
{
    public const STATUSES = [
        'all' => 'All',
        'completed' => 'Paid',
        'pending' => 'Pending',
        'failed' => 'Failed',
    ];

    public function index(Request $request)
    {
        $status = $this->allowedStatus($request);

        $payments = $this->filtered($request, $status)
            ->with('user')
            ->latest('updated_at')
            ->paginate(25)
            ->withQueryString();

        // Tab counts respect the search filters so the numbers match what each tab shows
        $counts = collect(self::STATUSES)->keys()
            ->mapWithKeys(fn ($key) => [$key => $this->filtered($request, $key)->count()]);

        $all = $this->filtered($request, 'all');
        $collected = (clone $all)->where('payment_status', 'completed')->sum('total_amount');
        $paidCount = $counts['completed'];

        return view('admin.payments.index', [
            'payments' => $payments,
            'status' => $status,
            'statuses' => self::STATUSES,
            'counts' => $counts,
            'totals' => [
                'collected' => $collected,
                'outstanding' => (clone $all)->where('payment_status', 'pending')->sum('total_amount'),
                'paid_count' => $paidCount,
                'average' => $paidCount ? $collected / $paidCount : 0,
            ],
            'courses' => TrainingObjective::orderBy('objective')->get(),
            'filters' => $request->only(['q', 'course', 'from', 'to']),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $status = $this->allowedStatus($request);
        $rows = $this->filtered($request, $status)->with('user')->latest('updated_at')->get();
        $courseNames = TrainingObjective::pluck('objective', 'id');

        $filename = 'bigrig-payments-' . strtolower(self::STATUSES[$status]) . '-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($rows, $courseNames) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Date', 'Student', 'Email', 'Phone', 'Courses', 'Method', 'Reference', 'Subtotal', 'Tax', 'Total', 'Status']);

            foreach ($rows as $payment) {
                fputcsv($out, [
                    $payment->updated_at?->format('Y-m-d H:i'),
                    trim("{$payment->user->firstName} {$payment->user->lastName}"),
                    $payment->user->email,
                    $payment->user->mobileNumber,
                    collect($payment->objectiveIds())->map(fn ($id) => $courseNames[$id] ?? $id)->join('; '),
                    $payment->payment_method,
                    $payment->payment_reference,
                    $payment->subtotal,
                    $payment->tax,
                    $payment->total_amount,
                    self::STATUSES[$payment->payment_status] ?? $payment->payment_status,
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function filtered(Request $request, string $status): Builder
    {
        $query = Subscription::query()->whereHas('user');

        if ($status !== 'all') {
            $query->where('payment_status', $status);
        }

        if ($search = trim((string) $request->input('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('payment_reference', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('firstName', 'like', "%{$search}%")
                        ->orWhere('lastName', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereRaw("CONCAT(firstName, ' ', lastName) LIKE ?", ["%{$search}%"]));
            });
        }

        if ($from = $this->date($request->input('from'))) {
            $query->where('updated_at', '>=', $from->startOfDay());
        }

        if ($to = $this->date($request->input('to'))) {
            $query->where('updated_at', '<=', $to->endOfDay());
        }

        if ($course = $request->integer('course')) {
            // Courses live in JSON; match the id as a whole number
            $query->where(function ($q) use ($course) {
                foreach (['[%d]', '[%d,%%', '%%,%d,%%', '%%,%d]'] as $pattern) {
                    $q->orWhere('objectives', 'like', sprintf($pattern, $course));
                }
            });
        }

        return $query;
    }

    private function date(?string $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function allowedStatus(Request $request): string
    {
        $status = $request->input('status', 'all');

        return array_key_exists($status, self::STATUSES) ? $status : 'all';
    }
}
