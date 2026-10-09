@section('title', 'Intake batches')
<x-app-layout>
    <x-portal.page title="Intake batches" subtitle="Each intake is a batch. New applicants join whichever batch is open.">
        <x-slot:actions>
            @can('manage_enrolment_batches')
                <button type="button" class="btn-brand btn-sm" @click="$dispatch('open-modal', 'batch-create')"><x-site.icon name="plus" class="h-4 w-4" /> New batch</button>
            @endcan
        </x-slot:actions>

        {{-- How opening a batch works --}}
        @if ($openBatch)
            <div class="mb-6 flex flex-col gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-900 sm:flex-row sm:items-center">
                <x-site.icon name="check-circle" class="h-5 w-5 shrink-0 text-green-600" />
                <p><strong>Admissions are open.</strong> Everyone who applies now joins <strong>{{ $openBatch->batch_name }}</strong>{{ $openBatch->c_date ? ', starting ' . \Illuminate\Support\Carbon::parse($openBatch->c_date)->format('j M Y') : '' }}. Only one batch can be open at a time — opening another batch closes this one.</p>
            </div>
        @else
            <div class="mb-6 flex flex-col gap-3 rounded-xl border border-brand-500 bg-brand-50 px-4 py-3 text-sm text-ink-900 sm:flex-row sm:items-center">
                <x-site.icon name="alert" class="h-5 w-5 shrink-0 text-brand-700" />
                <p><strong>Admissions are closed.</strong> No batch is open, so the website tells visitors that applications are closed. Open a batch below to start taking applications again.</p>
            </div>
        @endif

        <div class="portal-card">
            @if ($batches->isEmpty())
                <x-portal.empty icon="calendar" title="No batches yet" text="Create your first intake batch and open it to start taking applications.">
                    @can('manage_enrolment_batches')
                        <button type="button" class="btn-dark btn-sm mt-4" @click="$dispatch('open-modal', 'batch-create')"><x-site.icon name="plus" class="h-4 w-4" /> New batch</button>
                    @endcan
                </x-portal.empty>
            @else
                <div class="overflow-x-auto">
                    <table class="portal-table">
                        <thead>
                            <tr>
                                <th scope="col">Batch</th>
                                <th scope="col">Starts</th>
                                <th scope="col">Status</th>
                                <th scope="col" class="text-right">Applicants</th>
                                <th scope="col" class="text-right">Enrolled</th>
                                <th scope="col"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($batches as $batch)
                                @php $start = $batch->c_date ? \Illuminate\Support\Carbon::parse($batch->c_date) : null; @endphp
                                <tr>
                                    <td class="font-semibold text-ink-950">{{ $batch->batch_name }}</td>
                                    <td class="whitespace-nowrap">
                                        {{ $start?->format('j M Y') ?? '—' }}
                                        @if ($start)<span class="block text-xs text-ink-400">{{ $start->isFuture() ? 'in ' . $start->diffForHumans(null, true) : $start->diffForHumans() }}</span>@endif
                                    </td>
                                    <td>
                                        @if ($batch->active_batch)
                                            <span class="badge-green">Open for applications</span>
                                        @else
                                            <span class="badge-grey">Closed</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @can('read_applicants')
                                            <a href="{{ route('admin.admissions.index', ['status' => 'awaiting_payment', 'batch' => $batch->id]) }}" class="font-semibold text-ink-950 hover:underline">{{ number_format($applicantCounts[$batch->id] ?? 0) }}</a>
                                        @else
                                            {{ number_format($applicantCounts[$batch->id] ?? 0) }}
                                        @endcan
                                    </td>
                                    <td class="text-right font-semibold text-ink-950">{{ number_format($enrolledCounts[$batch->id] ?? 0) }}</td>
                                    <td>
                                        <div class="flex flex-wrap items-center justify-end gap-2">
                                            @if (Auth::user()->hasAnyPermission(['read_applicants', 'read_student_accounts']))
                                                <a href="{{ route('admin.admissions.index', ['status' => 'enrolled', 'batch' => $batch->id]) }}" class="btn-ghost btn-xs"><x-site.icon name="users" class="h-4 w-4" /> View students</a>
                                            @endif
                                            @can('manage_enrolment_batches')
                                                @unless ($batch->active_batch)
                                                    <form method="POST" action="{{ route('enrolment-batch.update', $batch) }}"
                                                          onsubmit="return confirm(@js('Open ' . $batch->batch_name . ' for applications?' . ($openBatch ? ' ' . $openBatch->batch_name . ' will be closed and new applicants will join ' . $batch->batch_name . ' instead.' : '')))">
                                                        @csrf @method('PATCH')
                                                        <button class="btn-dark btn-xs">Open for applications</button>
                                                    </form>
                                                @endunless
                                                <button type="button" class="btn-ghost btn-xs" @click="$dispatch('open-modal', 'batch-edit-{{ $batch->id }}')" aria-label="Edit {{ $batch->batch_name }}"><x-site.icon name="pencil" class="h-4 w-4" /></button>
                                                @if ($blockers[$batch->id])
                                                    <button type="button" class="btn-ghost btn-xs cursor-not-allowed text-ink-300" title="{{ ucfirst($blockers[$batch->id]) }}" aria-label="{{ $batch->batch_name }} can't be deleted: {{ $blockers[$batch->id] }}" @click="$dispatch('open-modal', 'batch-edit-{{ $batch->id }}')"><x-site.icon name="trash" class="h-4 w-4" /></button>
                                                @else
                                                    <form method="POST" action="{{ route('enrolment-batch.destroy', $batch) }}"
                                                          onsubmit="return confirm(@js('Delete ' . $batch->batch_name . '? This cannot be undone.' . ($batch->active_batch ? ' It is the open batch, so admissions will close until you open another.' : '')))">
                                                        @csrf @method('DELETE')
                                                        <button class="btn-ghost btn-xs text-red-600" aria-label="Delete {{ $batch->batch_name }}"><x-site.icon name="trash" class="h-4 w-4" /></button>
                                                    </form>
                                                @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        @can('manage_enrolment_batches')
            {{-- Create --}}
            <x-portal.modal name="batch-create" title="New batch">
                <form method="POST" action="{{ route('enrolment-batch.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="portal-label" for="batch_name">Batch name</label>
                        <input id="batch_name" name="batch_name" value="{{ old('batch_name') }}" required maxlength="255" placeholder="e.g. 2026 Batch B" class="portal-input">
                    </div>
                    <div>
                        <label class="portal-label" for="c_date">Start date</label>
                        <input id="c_date" type="date" name="c_date" value="{{ old('c_date') }}" required class="portal-input">
                    </div>
                    <label class="flex gap-2 text-sm">
                        <input type="checkbox" name="activate" value="1" @checked(old('activate', ! $openBatch)) class="mt-0.5 rounded border-ink-300 text-ink-950 focus:ring-brand-400">
                        <span>Open this batch for applications now{{ $openBatch ? ' (closes ' . $openBatch->batch_name . ')' : '' }}</span>
                    </label>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" class="btn-ghost btn-sm" @click="$dispatch('close-modal')">Cancel</button>
                        <button class="btn-brand btn-sm">Create batch</button>
                    </div>
                </form>
            </x-portal.modal>

            {{-- Edit, one per batch --}}
            @foreach ($batches as $batch)
                <x-portal.modal :name="'batch-edit-' . $batch->id" title="Edit batch">
                    <form method="POST" action="{{ route('admin.batches.update', $batch) }}" class="space-y-4">
                        @csrf @method('PUT')
                        <div>
                            <label class="portal-label" for="batch_name_{{ $batch->id }}">Batch name</label>
                            <input id="batch_name_{{ $batch->id }}" name="batch_name" value="{{ $batch->batch_name }}" required maxlength="255" class="portal-input">
                        </div>
                        <div>
                            <label class="portal-label" for="c_date_{{ $batch->id }}">Start date</label>
                            <input id="c_date_{{ $batch->id }}" type="date" name="c_date" value="{{ $batch->c_date ? \Illuminate\Support\Carbon::parse($batch->c_date)->format('Y-m-d') : '' }}" required class="portal-input">
                        </div>
                        @if ($blockers[$batch->id])
                            <p class="flex gap-2 rounded-lg bg-ink-50 px-3 py-2 text-xs text-ink-600"><x-site.icon name="alert" class="h-4 w-4 shrink-0" /> This batch can't be deleted because {{ $blockers[$batch->id] }}</p>
                        @endif
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" class="btn-ghost btn-sm" @click="$dispatch('close-modal')">Cancel</button>
                            <button class="btn-dark btn-sm">Save changes</button>
                        </div>
                    </form>
                </x-portal.modal>
            @endforeach
        @endcan
    </x-portal.page>
</x-app-layout>
