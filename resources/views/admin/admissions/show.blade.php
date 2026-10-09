@section('title', $student->full_name)
@php
    $currency = $settings->base_currency ?? '₦';
    $pending = $progress->pendingSubscription();
    $paid = $progress->paidSubscription();
    $current = $paid ?? $pending;
    $chosenIds = $current?->objectiveIds() ?? [];
    $medical = $student->medical;
    $labels = config('admissions.labels');
    $age = $student->date_of_birth?->age;
    $healthFlags = collect([
        'Vision' => $medical?->visual_impairment,
        'Hearing aid' => $medical?->hearing_aid,
        'Conditions' => $medical?->physical_disability,
        'Drug use' => $medical?->weed === 'Yes' ? 'Yes' : null,
        'Alcohol' => in_array($medical?->alcohol, ['Often', 'Daily User']) ? $medical->alcohol : null,
    ])->filter(fn ($v) => $v && $v !== 'None');
@endphp
<x-app-layout>
    <x-portal.page :title="$student->full_name" :back="route('admin.admissions.index', ['status' => $paid ? 'enrolled' : 'awaiting_payment'])">
        <x-slot:actions>
            <x-portal.status :status="$progress->status()" class="px-3 py-1 text-sm" />
            @can('update_suspend_user_account')
                <form method="POST" action="{{ route('user.deactivate', $student->id) }}" onsubmit="return confirm(@js($student->isSuspended() ? 'Reactivate this account?' : 'Suspend this account? They will be signed out and unable to sign in.'))">
                    @csrf @method('PATCH')
                    <button @class(['btn-sm', 'btn-ghost' => $student->isSuspended(), 'btn bg-white border-red-200 text-red-600 hover:border-red-400' => ! $student->isSuspended()])>
                        <x-site.icon :name="$student->isSuspended() ? 'refresh' : 'ban'" class="h-4 w-4" /> {{ $student->isSuspended() ? 'Reactivate' : 'Suspend' }}
                    </button>
                </form>
            @endcan
        </x-slot:actions>

        <div class="grid gap-6 xl:grid-cols-3">
            {{-- Left column --}}
            <div class="space-y-6 xl:col-span-2">
                {{-- Identity --}}
                <section class="portal-card p-5 sm:p-6">
                    <div class="flex flex-col gap-6 sm:flex-row">
                        @if ($student->profile_photo_path)
                            <a href="{{ Storage::url($student->profile_photo_path) }}" target="_blank" class="shrink-0" title="Open passport photograph">
                                <img src="{{ img($student->profile_photo_path, 300) }}" alt="Passport photograph of {{ $student->firstName }}" class="h-40 w-32 rounded-lg object-cover ring-1 ring-ink-100">
                            </a>
                        @else
                            <div class="flex h-40 w-32 shrink-0 items-center justify-center rounded-lg border-2 border-dashed border-red-200 bg-red-50 text-center text-xs font-semibold text-red-600">No passport photo</div>
                        @endif
                        <dl class="grid flex-1 gap-x-6 gap-y-3 text-sm sm:grid-cols-2">
                            @foreach ([
                                'Application' => '#' . str_pad($student->id, 5, '0', STR_PAD_LEFT) . ' · ' . $student->created_at?->format('j M Y'),
                                'Gender / age' => $student->gender . ($age ? " · {$age} years" : ''),
                                'Date of birth' => $student->date_of_birth?->format('j M Y'),
                                'NIN' => $student->nin,
                                'Phone' => $student->mobileNumber,
                                'Email' => $student->email . ($student->email_verified_at ? ' ✓' : ''),
                                'Address' => collect([$student->address, $student->city, $student->state])->filter()->join(', '),
                                'Driving licence' => $student->drivers_licence_number ? $student->drivers_licence_number . ($student->drivers_licence_expiry ? ' · expires ' . $student->drivers_licence_expiry->format('M Y') : '') : null,
                                'Next of kin' => $student->next_of_kin_name ? "{$student->next_of_kin_name} ({$student->next_of_kin_relationship}) · {$student->next_of_kin_phone}" : null,
                                'Intake batch' => $student->enrolmentBatch?->batch_name,
                            ] as $label => $value)
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wider text-ink-400">{{ $label }}</dt>
                                    <dd class="mt-0.5 break-words text-ink-900">{{ $value ?: '—' }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                    @if ($student->drivers_licence_expiry?->isPast())
                        <p class="mt-4 flex items-center gap-2 rounded-lg bg-red-50 px-3 py-2 text-sm font-medium text-red-800"><x-site.icon name="alert" class="h-4 w-4" /> Driving licence expired {{ $student->drivers_licence_expiry->diffForHumans() }}.</p>
                    @endif
                </section>

                {{-- Courses & instructors --}}
                <section class="portal-card" x-data="{ editing: false }">
                    <div class="portal-card-header">
                        <h2 class="portal-card-title">Courses &amp; instructors</h2>
                        @if ($pending && ! $paid)
                            @can('update_applicants')
                                <button type="button" class="text-sm font-semibold text-ink-600 underline decoration-brand-400 decoration-2 underline-offset-4" @click="editing = !editing" x-text="editing ? 'Cancel' : 'Change courses'">Change courses</button>
                            @endcan
                        @endif
                    </div>
                    <div class="p-5" x-show="!editing">
                        <ul class="divide-y divide-ink-100">
                            @forelse ($courses->whereIn('id', $chosenIds) as $course)
                                @php $assignment = $distributions->firstWhere('course_id', $course->id); @endphp
                                <li class="flex flex-col gap-3 py-3 first:pt-0 last:pb-0 md:flex-row md:items-center">
                                    <div class="flex-1">
                                        <p class="font-semibold text-ink-950">{{ $course->objective }}</p>
                                        <p class="text-xs text-ink-500">{{ $course->duration }} weeks · {{ $currency }}{{ number_format($course->price) }}</p>
                                    </div>
                                    @if ($paid)
                                        @can('update_applicants')
                                            <form method="POST" action="{{ route('admin.admissions.instructor', $student) }}" class="flex items-center gap-2">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="course_id" value="{{ $course->id }}">
                                                <label class="sr-only" for="instructor-{{ $course->id }}">Instructor for {{ $course->objective }}</label>
                                                <select id="instructor-{{ $course->id }}" name="instructor_id" class="portal-input min-w-[14rem] py-1.5" onchange="this.form.requestSubmit()">
                                                    @unless ($assignment)<option value="">Unassigned — choose instructor</option>@endunless
                                                    @foreach ($instructors as $instructor)
                                                        <option value="{{ $instructor->id }}" @selected($assignment?->instructor_id === $instructor->id)>{{ $instructor->firstName }} {{ $instructor->lastName }} — {{ $instructor->roles->firstWhere('portal', 'instructor')?->role_name }}</option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        @else
                                            <p class="text-sm">{{ $assignment?->instructor ? $assignment->instructor->firstName . ' ' . $assignment->instructor->lastName : 'Unassigned' }}</p>
                                        @endcan
                                    @else
                                        <span class="badge-grey">Assigned after payment</span>
                                    @endif
                                </li>
                            @empty
                                <li class="text-sm text-ink-500">No course selected.</li>
                            @endforelse
                        </ul>
                    </div>
                    @if ($pending && ! $paid)
                        @can('update_applicants')
                            <form method="POST" action="{{ route('admin.admissions.courses', $student) }}" class="space-y-2 p-5" x-show="editing" x-cloak>
                                @csrf @method('PUT')
                                @foreach ($courses as $course)
                                    <label class="flex items-center gap-3 rounded-lg border border-ink-100 p-3 has-[:checked]:border-ink-950 has-[:checked]:bg-brand-50">
                                        <input type="checkbox" name="selected_objective[]" value="{{ $course->id }}" @checked(in_array($course->id, $chosenIds)) class="rounded border-ink-300 text-ink-950 focus:ring-brand-400">
                                        <span class="flex-1 text-sm font-semibold">{{ $course->objective }}</span>
                                        <span class="text-sm">{{ $currency }}{{ number_format($course->price) }}</span>
                                    </label>
                                @endforeach
                                <button class="btn-dark btn-sm">Save and recalculate fees</button>
                            </form>
                        @endcan
                    @endif
                </section>

                {{-- Documents --}}
                <section class="portal-card">
                    <div class="portal-card-header"><h2 class="portal-card-title">Documents</h2></div>
                    @if ($student->documents->isEmpty())
                        <x-portal.empty icon="file-text" title="No documents uploaded" text="The applicant can upload their licence and NIN slip from their application page." />
                    @else
                        <ul class="divide-y divide-ink-100">
                            @foreach ($student->documents as $document)
                                <li class="flex flex-col gap-3 p-5 lg:flex-row lg:items-center" x-data="{ rejecting: false }">
                                    <div class="flex flex-1 items-center gap-3">
                                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-ink-50"><x-site.icon name="file-text" class="h-5 w-5" /></span>
                                        <div class="min-w-0">
                                            <a href="{{ route('application.documents.show', $document) }}" target="_blank" class="font-semibold text-ink-950 hover:underline">{{ $document->label }}</a>
                                            <p class="truncate text-xs text-ink-500">{{ $document->original_name }} · {{ $document->created_at->format('j M Y') }}
                                                @if ($document->reviewer) · {{ ucfirst($document->status) }} by {{ $document->reviewer->firstName }}@endif
                                            </p>
                                            @if ($document->review_note)<p class="text-xs text-red-700">{{ $document->review_note }}</p>@endif
                                        </div>
                                    </div>
                                    <x-portal.status :status="$document->status" />
                                    @can('update_applicants')
                                        <div class="flex flex-wrap items-center gap-2">
                                            @if ($document->status !== 'approved')
                                                <form method="POST" action="{{ route('admin.admissions.document', [$student, $document]) }}">
                                                    @csrf @method('PUT')
                                                    <input type="hidden" name="status" value="approved">
                                                    <button class="btn-ghost btn-xs text-green-700"><x-site.icon name="check" class="h-4 w-4" /> Approve</button>
                                                </form>
                                            @endif
                                            @if ($document->status !== 'rejected')
                                                <button type="button" class="btn-ghost btn-xs text-red-600" @click="rejecting = !rejecting">Reject…</button>
                                            @endif
                                        </div>
                                        <form method="POST" action="{{ route('admin.admissions.document', [$student, $document]) }}" class="flex w-full gap-2 lg:w-auto" x-show="rejecting" x-cloak>
                                            @csrf @method('PUT')
                                            <input type="hidden" name="status" value="rejected">
                                            <input name="review_note" required maxlength="255" placeholder="Reason, e.g. image unreadable" class="portal-input py-1.5">
                                            <button class="btn-danger btn-xs">Reject</button>
                                        </form>
                                    @endcan
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>

                {{-- Health --}}
                <section class="portal-card">
                    <div class="portal-card-header">
                        <h2 class="portal-card-title">Health declaration</h2>
                        @if ($healthFlags->isNotEmpty())<span class="badge-yellow"><x-site.icon name="alert" class="h-3.5 w-3.5" /> {{ $healthFlags->count() }} to review</span>@endif
                    </div>
                    @if ($medical)
                        <dl class="grid gap-x-6 gap-y-4 p-5 text-sm sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ([
                                'Weight' => $medical->weight ? rtrim(rtrim($medical->weight, '0'), '.') . ' kg' : null,
                                'Height' => $medical->height ? rtrim(rtrim($medical->height, '0'), '.') . ' ft' : null,
                                'Vision' => $labels['visual_impairment'][$medical->visual_impairment] ?? $medical->visual_impairment,
                                'Hearing aid' => $labels['hearing_aid'][$medical->hearing_aid] ?? $medical->hearing_aid,
                                'Conditions' => $labels['physical_disability'][$medical->physical_disability] ?? $medical->physical_disability,
                                'Drug use' => $medical->weed,
                                'Alcohol' => $labels['alcohol'][$medical->alcohol] ?? $medical->alcohol,
                                'Medication' => $medical->prescribed_medication,
                                'Failed drug test' => $medical->failed_drug_test,
                            ] as $label => $value)
                                <div @class(['rounded-lg bg-brand-50 p-2 -m-2' => $healthFlags->has($label)])>
                                    <dt class="text-xs font-semibold uppercase tracking-wider text-ink-400">{{ $label }}</dt>
                                    <dd class="mt-0.5 text-ink-900">{{ $value ?: '—' }}</dd>
                                </div>
                            @endforeach
                        </dl>
                        <p class="border-t border-ink-100 px-5 py-3 text-xs text-ink-400">{{ $medical->consent_given_at ? 'Consent given ' . $medical->consent_given_at->format('j M Y, g:ia') : 'Submitted before consent was recorded' }}</p>
                    @else
                        <x-portal.empty icon="clipboard" title="No health declaration on file" />
                    @endif
                </section>
            </div>

            {{-- Right column --}}
            <div class="space-y-6">
                {{-- Progress --}}
                <section class="portal-card p-5">
                    <h2 class="portal-card-title">Progress</h2>
                    <ol class="mt-4 space-y-3">
                        @foreach ($progress->steps() as $step)
                            <li class="flex gap-3">
                                <span @class(['mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full', 'bg-ink-950 text-brand-400' => $step['state'] === 'done', 'bg-brand-400' => $step['state'] === 'current', 'bg-ink-100' => $step['state'] === 'upcoming'])>
                                    @if ($step['state'] === 'done')<x-site.icon name="check" class="h-3 w-3" />@endif
                                </span>
                                <div>
                                    <p @class(['text-sm font-semibold', 'text-ink-400' => $step['state'] === 'upcoming', 'text-ink-950' => $step['state'] !== 'upcoming'])>{{ $step['title'] }}</p>
                                    <p class="text-xs text-ink-500">{{ $step['detail'] }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </section>

                {{-- Payment --}}
                <section class="portal-card">
                    <div class="portal-card-header"><h2 class="portal-card-title">Fees</h2></div>
                    <div class="p-5">
                        @if ($current)
                            <dl class="space-y-1.5 text-sm">
                                <div class="flex justify-between"><dt class="text-ink-500">Tuition</dt><dd>{{ $currency }}{{ number_format($current->subtotal, 2) }}</dd></div>
                                @if ($current->discount > 0)
                                    <div class="flex justify-between text-green-700"><dt>Combo discount</dt><dd>−{{ $currency }}{{ number_format($current->discount, 2) }}</dd></div>
                                @endif
                                <div class="flex justify-between"><dt class="text-ink-500">VAT</dt><dd>{{ $currency }}{{ number_format($current->tax, 2) }}</dd></div>
                                <div class="flex justify-between border-t border-ink-100 pt-2 font-semibold text-ink-950"><dt>Total</dt><dd class="font-display text-2xl">{{ $currency }}{{ number_format($current->total_amount, 2) }}</dd></div>
                            </dl>
                        @endif

                        @if ($paid)
                            <div class="mt-4 rounded-lg bg-green-50 p-3 text-sm text-green-900">
                                <p class="font-semibold">Paid {{ $paid->updated_at->format('j M Y') }}</p>
                                <p class="break-all text-xs">{{ $paid->payment_method }} · {{ $paid->payment_reference }}</p>
                            </div>
                        @elseif ($pending)
                            @can('create_payments')
                                <form method="POST" action="{{ route('admin.admissions.payment', $student) }}" class="mt-5 space-y-3 border-t border-ink-100 pt-5" x-data="{ open: {{ $errors->hasAny(['method', 'reference', 'confirm_amount']) ? 'true' : 'false' }} }">
                                    @csrf
                                    <button type="button" class="btn-dark btn-sm w-full" x-show="!open" @click="open = true">Record offline payment</button>
                                    <div x-show="open" x-cloak class="space-y-3">
                                        <div>
                                            <label class="portal-label" for="method">Payment method</label>
                                            <select id="method" name="method" class="portal-input" required>
                                                @foreach (['Bank transfer', 'Cash', 'POS', 'Paystack (manual)'] as $method)<option @selected(old('method') === $method)>{{ $method }}</option>@endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="portal-label" for="reference">Receipt / transfer reference</label>
                                            <input id="reference" name="reference" value="{{ old('reference') }}" required maxlength="100" class="portal-input">
                                        </div>
                                        <label class="flex gap-2 text-sm"><input type="checkbox" name="confirm_amount" value="1" required class="mt-0.5 rounded border-ink-300 text-ink-950 focus:ring-brand-400"> I confirm {{ $currency }}{{ number_format($pending->total_amount, 2) }} has been received in full.</label>
                                        <button class="btn-brand btn-sm w-full">Confirm payment &amp; enrol</button>
                                    </div>
                                </form>
                            @endcan
                        @endif
                    </div>
                </section>

                {{-- Batch --}}
                <section class="portal-card p-5">
                    <h2 class="portal-card-title">Intake batch</h2>
                    @can('update_applicants')
                        <form method="POST" action="{{ route('admin.admissions.batch', $student) }}" class="mt-3 flex gap-2">
                            @csrf @method('PUT')
                            <label class="sr-only" for="batch">Batch</label>
                            <select id="batch" name="enrolment_batch_id" class="portal-input">
                                @foreach ($batches as $batch)<option value="{{ $batch->id }}" @selected($student->enrolment_batch_id === $batch->id)>{{ $batch->batch_name }}{{ $batch->active_batch ? ' (current)' : '' }}</option>@endforeach
                            </select>
                            <button class="btn-ghost btn-sm">Move</button>
                        </form>
                    @else
                        <p class="mt-2 text-sm">{{ $student->enrolmentBatch?->batch_name ?? '—' }}</p>
                    @endcan
                </section>

                {{-- Attendance --}}
                @if ($paid)
                    <section class="portal-card p-5">
                        <h2 class="portal-card-title">Attendance</h2>
                        @php $total = $attendance->sum(); @endphp
                        @if ($total)
                            <p class="mt-2 font-display text-4xl font-bold text-ink-950">{{ round((($attendance['present'] ?? 0) + ($attendance['late'] ?? 0)) / $total * 100) }}%</p>
                            <p class="text-xs text-ink-500">{{ $attendance['present'] ?? 0 }} present · {{ $attendance['late'] ?? 0 }} late · {{ $attendance['absent'] ?? 0 }} absent · {{ $attendance['excused'] ?? 0 }} excused</p>
                        @else
                            <p class="mt-2 text-sm text-ink-500">No sessions recorded yet.</p>
                        @endif
                    </section>
                @endif

                {{-- Account --}}
                <section class="portal-card p-5 text-sm">
                    <h2 class="portal-card-title">Account</h2>
                    <dl class="mt-3 space-y-2">
                        <div class="flex justify-between gap-3"><dt class="text-ink-500">Email verified</dt><dd>{{ $student->email_verified_at ? $student->email_verified_at->format('j M Y') : 'No' }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-ink-500">Portal access</dt><dd>{{ $student->isSuspended() ? 'Suspended' : ($paid ? 'Full' : 'Application only') }}</dd></div>
                    </dl>
                    @if (! $student->email_verified_at)
                        @can('update_verify_user_account')
                            <form method="POST" action="{{ route('user.verify', $student->id) }}" class="mt-3">
                                @csrf @method('PATCH')
                                <button class="btn-ghost btn-xs">Mark email as verified</button>
                            </form>
                        @endcan
                    @endif
                </section>
            </div>
        </div>
    </x-portal.page>
</x-app-layout>
