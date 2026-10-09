@section('title', 'Payments')
@php
    $currency = $settings->base_currency ?? '₦';
    $money = fn ($n) => $currency . number_format((float) $n);
    $hasFilters = (bool) array_filter($filters);
@endphp
<x-app-layout>
    <x-portal.page title="Payments" subtitle="Every fee record — paid, waiting for payment or failed. Filter by date or course and export what you see.">
        <x-slot:actions>
            @can('export_payments')
                <a href="{{ route('admin.payments.export', request()->query()) }}" class="btn-ghost btn-sm"><x-site.icon name="download" class="h-4 w-4" /> Export CSV</a>
            @endcan
        </x-slot:actions>

        {{-- Totals for the current filters --}}
        <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-portal.stat label="Fees collected" :value="$money($totals['collected'])" icon="credit-card" :hint="$hasFilters ? 'For the current filters' : 'All time'" />
            <x-portal.stat label="Outstanding (pending)" :value="$money($totals['outstanding'])" icon="clock" hint="Applied but not yet paid" />
            <x-portal.stat label="Payments received" :value="number_format($totals['paid_count'])" icon="check-circle" />
            <x-portal.stat label="Average payment" :value="$money(round($totals['average']))" icon="trending-up" />
        </div>

        {{-- Status tabs --}}
        <nav class="mb-6 flex gap-1 overflow-x-auto rounded-xl bg-white p-1 shadow-sm ring-1 ring-ink-100" aria-label="Payment status">
            @foreach ($statuses as $key => $label)
                <a href="{{ route('payments', array_merge($filters, ['status' => $key])) }}"
                   @class(['flex shrink-0 items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition', 'bg-ink-950 text-white' => $status === $key, 'text-ink-600 hover:bg-ink-50' => $status !== $key])
                   @if ($status === $key) aria-current="page" @endif>
                    {{ $label }}
                    <span @class(['rounded-full px-2 py-0.5 text-xs', 'bg-brand-400 text-ink-950' => $status === $key, 'bg-ink-100 text-ink-600' => $status !== $key])>{{ $counts[$key] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="portal-card">
            {{-- Filters --}}
            <form method="GET" class="grid gap-3 border-b border-ink-100 p-4 sm:grid-cols-2 lg:grid-cols-[1fr_14rem_10rem_10rem_auto]">
                <input type="hidden" name="status" value="{{ $status }}">
                <label class="relative">
                    <span class="sr-only">Search</span>
                    <x-site.icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-400" />
                    <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Student name, email or reference" class="portal-input pl-9">
                </label>
                <label>
                    <span class="sr-only">Course</span>
                    <select name="course" class="portal-input">
                        <option value="">All courses</option>
                        @foreach ($courses as $course)<option value="{{ $course->id }}" @selected(($filters['course'] ?? null) == $course->id)>{{ $course->objective }}</option>@endforeach
                    </select>
                </label>
                <label>
                    <span class="sr-only">From date</span>
                    <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="portal-input" title="From date" aria-label="From date">
                </label>
                <label>
                    <span class="sr-only">To date</span>
                    <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="portal-input" title="To date" aria-label="To date">
                </label>
                <div class="flex gap-2">
                    <button class="btn-dark btn-sm flex-1"><x-site.icon name="filter" class="h-4 w-4" /> Filter</button>
                    @if ($hasFilters)
                        <a href="{{ route('payments', ['status' => $status]) }}" class="btn-ghost btn-sm">Clear</a>
                    @endif
                </div>
            </form>

            @if ($payments->isEmpty())
                <x-portal.empty icon="credit-card" title="No payments found" text="{{ $hasFilters ? 'Nothing matches these filters. Try a wider date range or clear the search.' : 'Fee records appear here as soon as someone applies.' }}" />
            @else
                <div class="overflow-x-auto">
                    <table class="portal-table">
                        <thead>
                            <tr>
                                <th scope="col">Student</th>
                                <th scope="col">Courses</th>
                                <th scope="col">Method</th>
                                <th scope="col">Reference</th>
                                <th scope="col" class="text-right">Amount</th>
                                <th scope="col">Status</th>
                                <th scope="col">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.admissions.show', $payment->user) }}" class="flex items-center gap-3">
                                            <x-portal.avatar :user="$payment->user" size="h-9 w-9" />
                                            <span class="min-w-0">
                                                <span class="block font-semibold text-ink-950 hover:underline">{{ $payment->user->firstName }} {{ $payment->user->lastName }}</span>
                                                <span class="block max-w-[14rem] truncate text-xs text-ink-400">{{ $payment->user->email }}</span>
                                            </span>
                                        </a>
                                    </td>
                                    <td class="max-w-[16rem]">
                                        <span class="line-clamp-2">{{ $courses->whereIn('id', $payment->objectiveIds())->pluck('objective')->join(', ') ?: '—' }}</span>
                                    </td>
                                    <td class="max-w-[12rem] text-ink-600"><span class="line-clamp-2">{{ $payment->payment_method ?: '—' }}</span></td>
                                    <td class="whitespace-nowrap font-mono text-xs text-ink-600">{{ $payment->payment_reference ?: '—' }}</td>
                                    <td class="whitespace-nowrap text-right font-semibold text-ink-950">{{ $money($payment->total_amount) }}</td>
                                    <td><x-portal.status :status="$payment->payment_status" /></td>
                                    <td class="whitespace-nowrap text-ink-500" title="{{ $payment->updated_at }}">{{ $payment->updated_at?->format('j M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($payments->hasPages())
                    <div class="border-t border-ink-100 px-4 py-3">{{ $payments->links() }}</div>
                @endif
            @endif
        </div>
    </x-portal.page>
</x-app-layout>
