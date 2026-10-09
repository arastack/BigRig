@section('title', 'Dashboard')
@php
    $currency = $settings->base_currency ?? '₦';
    $money = fn ($n) => $currency . ($n >= 1000000 ? rtrim(rtrim(number_format($n / 1000000, 1), '0'), '.') . 'm' : number_format($n));
    $canStudents = $user->hasAnyPermission(['read_dashboard_user_card', 'read_applicants', 'read_student_accounts']);
    $canRevenue = $user->hasAnyPermission(['read_dashboard_revenue_card', 'read_payments']);
@endphp
<x-app-layout>
    <x-portal.page :title="'Good ' . (now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening')) . ', ' . $user->firstName"
        :subtitle="$activeBatch ? 'Current intake: ' . $activeBatch->batch_name . '. Here is what needs your attention.' : 'No intake batch is open — applications are closed until you activate one.'">
        <x-slot:actions>
            @can('read_applicants')<a href="{{ route('admin.admissions.index') }}" class="btn-dark btn-sm">Review applicants</a>@endcan
        </x-slot:actions>

        {{-- Attention strip --}}
        @php
            $alerts = collect([
                ! $activeBatch && $user->hasAnyPermission(['manage_enrolment_batches']) ? ['Admissions are closed — no active intake batch.', route('enrolment-batches'), 'Open a batch'] : null,
                $metrics['unreadEnquiries'] && $user->hasPermission('read_contact_messages') ? [$metrics['unreadEnquiries'] . ' unread website ' . \Illuminate\Support\Str::plural('enquiry', $metrics['unreadEnquiries']), Route::has('admin.enquiries.index') ? route('admin.enquiries.index') : null, 'Read'] : null,
                $metrics['pendingChanges'] && $user->hasPermission('manage_schedule_change_requests') ? [$metrics['pendingChanges'] . ' schedule change ' . \Illuminate\Support\Str::plural('request', $metrics['pendingChanges']) . ' awaiting a decision', Route::has('admin.change-requests.index') ? route('admin.change-requests.index') : null, 'Review'] : null,
            ])->filter();
        @endphp
        @if ($alerts->isNotEmpty())
            <div class="mb-6 space-y-2">
                @foreach ($alerts as [$text, $url, $cta])
                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-brand-500 bg-brand-50 px-4 py-3 text-sm">
                        <p class="flex items-center gap-2 font-medium text-ink-900"><x-site.icon name="alert" class="h-4 w-4 text-brand-700" /> {{ $text }}</p>
                        @if ($url)<a href="{{ $url }}" class="font-semibold text-ink-950 underline underline-offset-4">{{ $cta }}</a>@endif
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Key numbers --}}
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @if ($canStudents)
                <x-portal.stat label="Applicants awaiting payment" :value="number_format($metrics['applicants'])" icon="file-text"
                    :hint="$metrics['applicantsThisWeek'] . ' new this week'" :href="Route::has('admin.admissions.index') && $user->hasPermission('read_applicants') ? route('admin.admissions.index') : null" />
                <x-portal.stat label="Enrolled students" :value="number_format($metrics['enrolled'])" icon="graduation"
                    :hint="$metrics['conversion'] . '% of applicants enrol'" :href="route('admin.admissions.index', ['status' => 'enrolled'])" />
            @endif
            @if ($canRevenue)
                <x-portal.stat label="Fees collected" :value="$money($metrics['revenue'])" icon="credit-card"
                    :hint="$money($metrics['revenueThisMonth']) . ' this month'" :href="$user->hasPermission('read_payments') ? route('payments') : null" />
                <x-portal.stat label="Outstanding from applicants" :value="$money($metrics['outstanding'])" icon="trending-up" hint="If every applicant pays" />
            @endif
            @if (! $canStudents && ! $canRevenue)
                <x-portal.stat label="Sessions today" :value="$metrics['sessionsToday']" icon="calendar" />
                <x-portal.stat label="Instructors" :value="$metrics['instructors']" icon="id-card" />
            @endif
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-3">
            {{-- Trend chart --}}
            @if ($canStudents)
                <section class="portal-card xl:col-span-2">
                    <div class="portal-card-header">
                        <h2 class="portal-card-title">Applications &amp; enrolments</h2>
                        <span class="text-xs text-ink-400">Last 12 months</span>
                    </div>
                    <div class="h-72 p-5"><canvas id="trend-chart" aria-label="Applications and enrolments per month" role="img"></canvas></div>
                </section>
            @endif

            {{-- Course demand --}}
            @if ($canStudents)
                <section class="portal-card">
                    <div class="portal-card-header"><h2 class="portal-card-title">Demand by course</h2></div>
                    <ul class="space-y-4 p-5">
                        @php $max = max(1, $demand->max('applied')); @endphp
                        @forelse ($demand as $row)
                            <li>
                                <div class="flex items-baseline justify-between gap-3 text-sm">
                                    <span class="font-medium text-ink-900">{{ $row['course'] }}</span>
                                    <span class="shrink-0 text-ink-500"><strong class="text-ink-950">{{ $row['applied'] }}</strong> applied · {{ $row['paid'] }} paid</span>
                                </div>
                                <div class="mt-1.5 h-2.5 overflow-hidden rounded-full bg-ink-100">
                                    <div class="relative h-full rounded-full bg-brand-400" style="width: {{ $row['applied'] / $max * 100 }}%">
                                        <div class="absolute inset-y-0 left-0 rounded-full bg-ink-950" style="width: {{ $row['applied'] ? $row['paid'] / $row['applied'] * 100 : 0 }}%"></div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="text-sm text-ink-500">No applications yet.</li>
                        @endforelse
                    </ul>
                    <p class="flex gap-4 border-t border-ink-100 px-5 py-3 text-xs text-ink-500"><span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-ink-950"></span>Paid</span><span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-brand-400"></span>Applied, unpaid</span></p>
                </section>
            @endif

            {{-- Recent applicants --}}
            @if ($canStudents)
                <section class="portal-card xl:col-span-2">
                    <div class="portal-card-header">
                        <h2 class="portal-card-title">Latest applications</h2>
                        <a href="{{ route('admin.admissions.index', ['status' => 'all']) }}" class="text-sm font-semibold text-ink-600 hover:text-ink-950">View all</a>
                    </div>
                    <ul class="divide-y divide-ink-100">
                        @forelse ($recentApplicants as $applicant)
                            @php $isPaid = $applicant->subscriptions->contains('payment_status', 'completed'); @endphp
                            <li>
                                <a href="{{ route('admin.admissions.show', $applicant) }}" class="flex items-center gap-3 px-5 py-3 hover:bg-ink-50">
                                    <x-portal.avatar :user="$applicant" size="h-9 w-9" />
                                    <span class="min-w-0 flex-1">
                                        <span class="block truncate font-semibold text-ink-950">{{ $applicant->firstName }} {{ $applicant->lastName }}</span>
                                        <span class="block truncate text-xs text-ink-500">{{ $applicant->email }}</span>
                                    </span>
                                    <x-portal.status :status="$applicant->suspended_at ? 'suspended' : ($isPaid ? 'enrolled' : 'awaiting_payment')" />
                                    <span class="hidden w-24 text-right text-xs text-ink-400 sm:block">{{ $applicant->created_at?->diffForHumans(short: true) }}</span>
                                </a>
                            </li>
                        @empty
                            <li><x-portal.empty title="No applications yet" /></li>
                        @endforelse
                    </ul>
                </section>
            @endif

            {{-- Upcoming sessions --}}
            <section class="portal-card">
                <div class="portal-card-header">
                    <h2 class="portal-card-title">Next 7 days</h2>
                    @can('read_training_schedule')<a href="{{ route('schedule') }}" class="text-sm font-semibold text-ink-600 hover:text-ink-950">Schedule</a>@endcan
                </div>
                <ul class="divide-y divide-ink-100">
                    @forelse ($upcoming as $session)
                        <li class="flex gap-4 px-5 py-3">
                            <div class="w-12 shrink-0 text-center">
                                <p class="text-xs font-semibold uppercase text-ink-400">{{ $session->schedule_date->format('D') }}</p>
                                <p class="font-display text-2xl font-bold leading-none text-ink-950">{{ $session->schedule_date->format('j') }}</p>
                            </div>
                            <div class="min-w-0 text-sm">
                                <p class="truncate font-semibold text-ink-950">{{ $session->topic?->topic ?? $session->course?->objective }}</p>
                                <p class="truncate text-xs text-ink-500">{{ \Carbon\Carbon::parse($session->time_start)->format('g:ia') }} · {{ $session->instructor?->firstName }} · {{ count($session->studentIds()) }} students</p>
                            </div>
                        </li>
                    @empty
                        <li><x-portal.empty icon="calendar" title="Nothing scheduled" text="No training sessions in the next week." /></li>
                    @endforelse
                </ul>
            </section>

            {{-- Recent payments --}}
            @if ($canRevenue)
                <section class="portal-card xl:col-span-2">
                    <div class="portal-card-header">
                        <h2 class="portal-card-title">Recent payments</h2>
                        @can('read_payments')<a href="{{ route('payments') }}" class="text-sm font-semibold text-ink-600 hover:text-ink-950">All payments</a>@endcan
                    </div>
                    @if ($recentPayments->isEmpty())
                        <x-portal.empty icon="credit-card" title="No payments yet" />
                    @else
                        <div class="overflow-x-auto">
                            <table class="portal-table">
                                <thead><tr><th>Student</th><th>Method</th><th>Date</th><th class="text-right">Amount</th></tr></thead>
                                <tbody>
                                    @foreach ($recentPayments as $payment)
                                        <tr>
                                            <td class="font-semibold text-ink-950">{{ $payment->user?->firstName }} {{ $payment->user?->lastName }}</td>
                                            <td>{{ \Illuminate\Support\Str::before($payment->payment_method, ' ·') }}</td>
                                            <td class="whitespace-nowrap">{{ $payment->updated_at->format('j M Y') }}</td>
                                            <td class="text-right font-semibold text-ink-950">{{ $currency }}{{ number_format($payment->total_amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>
            @endif

            {{-- Team --}}
            @can('read_dashboard_instructor_card')
                <section class="portal-card p-5">
                    <h2 class="portal-card-title">Team</h2>
                    <dl class="mt-4 grid grid-cols-2 gap-4">
                        <div><dt class="text-sm text-ink-500">Instructors</dt><dd class="font-display text-4xl font-bold text-ink-950">{{ $metrics['instructors'] }}</dd></div>
                        <div><dt class="text-sm text-ink-500">All staff</dt><dd class="font-display text-4xl font-bold text-ink-950">{{ $metrics['staff'] }}</dd></div>
                        <div><dt class="text-sm text-ink-500">Sessions today</dt><dd class="font-display text-4xl font-bold text-ink-950">{{ $metrics['sessionsToday'] }}</dd></div>
                    </dl>
                    @can('read_instructors')<a href="{{ route('instructors') }}" class="btn-ghost btn-sm mt-5 w-full">Manage staff</a>@endcan
                </section>
            @endcan
        </div>
    </x-portal.page>

    @if ($canStudents)
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const trend = @js($trend);
                    new Chart(document.getElementById('trend-chart'), {
                        type: 'bar',
                        data: {
                            labels: trend.labels,
                            datasets: [
                                { label: 'Applications', data: trend.applications, backgroundColor: '#FFE500', borderRadius: 4, maxBarThickness: 22 },
                                { label: 'Enrolments', data: trend.enrolments, backgroundColor: '#111110', borderRadius: 4, maxBarThickness: 22 },
                            ],
                        },
                        options: {
                            maintainAspectRatio: false,
                            plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } } },
                            scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#E9E9E7' } } },
                        },
                    });
                });
            </script>
        @endpush
    @endif
</x-app-layout>
