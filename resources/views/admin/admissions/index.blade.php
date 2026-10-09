@section('title', $status === 'awaiting_payment' ? 'Applicants' : 'Students')
@php $currency = $settings->base_currency ?? '₦'; @endphp
<x-app-layout>
    <x-portal.page :title="$status === 'awaiting_payment' ? 'Applicants' : 'Students'"
        :subtitle="$status === 'awaiting_payment' ? 'People who have applied but not yet paid. Follow up, record offline payments or update their courses.' : 'Everyone who has applied or enrolled, with their status, intake and fees.'">
        <x-slot:actions>
            @can('export_students')
                <a href="{{ route('admin.admissions.export', request()->query()) }}" class="btn-ghost btn-sm"><x-site.icon name="download" class="h-4 w-4" /> Export CSV</a>
            @endcan
        </x-slot:actions>

        {{-- Status tabs --}}
        <nav class="mb-6 flex gap-1 overflow-x-auto rounded-xl bg-white p-1 shadow-sm ring-1 ring-ink-100" aria-label="Status">
            @foreach ($statuses as $key => $label)
                <a href="{{ route('admin.admissions.index', array_merge($filters, ['status' => $key])) }}"
                   @class(['flex shrink-0 items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition', 'bg-ink-950 text-white' => $status === $key, 'text-ink-600 hover:bg-ink-50' => $status !== $key])
                   @if ($status === $key) aria-current="page" @endif>
                    {{ $label }}
                    <span @class(['rounded-full px-2 py-0.5 text-xs', 'bg-brand-400 text-ink-950' => $status === $key, 'bg-ink-100 text-ink-600' => $status !== $key])>{{ $counts[$key] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="portal-card">
            {{-- Filters --}}
            <form method="GET" class="grid gap-3 border-b border-ink-100 p-4 sm:grid-cols-2 lg:grid-cols-[1fr_14rem_12rem_auto]">
                <input type="hidden" name="status" value="{{ $status }}">
                <label class="relative">
                    <span class="sr-only">Search</span>
                    <x-site.icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-400" />
                    <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Name, email, phone, NIN or application #" class="portal-input pl-9">
                </label>
                <label>
                    <span class="sr-only">Course</span>
                    <select name="course" class="portal-input">
                        <option value="">All courses</option>
                        @foreach ($courses as $course)<option value="{{ $course->id }}" @selected(($filters['course'] ?? null) == $course->id)>{{ $course->objective }}</option>@endforeach
                    </select>
                </label>
                <label>
                    <span class="sr-only">Batch</span>
                    <select name="batch" class="portal-input">
                        <option value="">All batches</option>
                        @foreach ($batches as $batch)<option value="{{ $batch->id }}" @selected(($filters['batch'] ?? null) == $batch->id)>{{ $batch->batch_name }}</option>@endforeach
                    </select>
                </label>
                <div class="flex gap-2">
                    <button class="btn-dark btn-sm flex-1"><x-site.icon name="filter" class="h-4 w-4" /> Filter</button>
                    @if (array_filter($filters))
                        <a href="{{ route('admin.admissions.index', ['status' => $status]) }}" class="btn-ghost btn-sm">Clear</a>
                    @endif
                </div>
            </form>

            @if ($students->isEmpty())
                <x-portal.empty icon="graduation" title="Nobody here yet" text="{{ array_filter($filters) ? 'No one matches these filters.' : 'New applications will appear here as they come in.' }}" />
            @else
                <div class="overflow-x-auto">
                    <table class="portal-table">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Contact</th>
                                <th scope="col">Courses</th>
                                <th scope="col">Batch</th>
                                <th scope="col" class="text-right">Fees</th>
                                <th scope="col">Status</th>
                                <th scope="col">Applied</th>
                                <th scope="col"><span class="sr-only">Open</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $student)
                                @php
                                    $subscription = $student->subscriptions->firstWhere('payment_status', 'completed') ?? $student->subscriptions->first();
                                    $state = $student->suspended_at ? 'suspended' : ($student->subscriptions->contains('payment_status', 'completed') ? 'enrolled' : 'awaiting_payment');
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.admissions.show', $student) }}" class="flex items-center gap-3">
                                            <x-portal.avatar :user="$student" size="h-9 w-9" />
                                            <span>
                                                <span class="block font-semibold text-ink-950 hover:underline">{{ $student->firstName }} {{ $student->lastName }}</span>
                                                <span class="block text-xs text-ink-400">#{{ str_pad($student->id, 5, '0', STR_PAD_LEFT) }}</span>
                                            </span>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="block">{{ $student->mobileNumber }}</span>
                                        <span class="block max-w-[14rem] truncate text-xs text-ink-400">{{ $student->email }}</span>
                                    </td>
                                    <td class="max-w-[16rem]">
                                        <span class="line-clamp-2">{{ $courses->whereIn('id', $subscription?->objectiveIds() ?? [])->pluck('objective')->join(', ') ?: '—' }}</span>
                                    </td>
                                    <td class="whitespace-nowrap">{{ $student->enrolmentBatch?->batch_name ?? '—' }}</td>
                                    <td class="whitespace-nowrap text-right font-semibold text-ink-950">{{ $subscription ? $currency . number_format($subscription->total_amount) : '—' }}</td>
                                    <td><x-portal.status :status="$state" /></td>
                                    <td class="whitespace-nowrap text-ink-500" title="{{ $student->created_at }}">{{ $student->created_at?->diffForHumans() }}</td>
                                    <td class="text-right"><a href="{{ route('admin.admissions.show', $student) }}" class="btn-ghost btn-xs" aria-label="Open {{ $student->firstName }}'s record"><x-site.icon name="chevron-right" class="h-4 w-4" /></a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($students->hasPages())
                    <div class="border-t border-ink-100 px-4 py-3">{{ $students->links() }}</div>
                @endif
            @endif
        </div>
    </x-portal.page>
</x-app-layout>
