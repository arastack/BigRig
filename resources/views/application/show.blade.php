@php
    $currency = $settings->base_currency ?? '₦';
    $isPaid = $subscription?->isPaid();
    $chosenIds = $subscription?->objectiveIds() ?? [];
    $chosen = $courses->whereIn('id', $chosenIds);
    $documentTypes = \App\Models\ApplicantDocument::TYPES;
    $statusStyles = [
        'awaiting_payment' => 'bg-brand-400 text-ink-950',
        'enrolled' => 'bg-green-600 text-white',
        'in_training' => 'bg-green-600 text-white',
        'suspended' => 'bg-red-600 text-white',
    ];
@endphp
<x-site.auth title="My application" :wide="true">
    {{-- Header --}}
    <div class="on-dark bg-ink-950 text-white">
        <div class="container-site flex flex-col gap-6 py-10 sm:flex-row sm:items-end sm:justify-between sm:py-12">
            <div class="flex items-center gap-5">
                @if ($user->profile_photo_path)
                    <img src="{{ img($user->profile_photo_path, 200) }}" alt="" class="h-20 w-20 rounded-lg object-cover ring-2 ring-brand-400">
                @endif
                <div>
                    <p class="eyebrow">Application #{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</p>
                    <h1 class="mt-2 text-4xl text-white sm:text-5xl">Hello, {{ $user->firstName }}</h1>
                </div>
            </div>
            <span class="inline-flex items-center gap-2 self-start rounded-full px-4 py-1.5 font-display text-lg font-bold uppercase tracking-wider sm:self-auto {{ $statusStyles[$progress->status()] }}">{{ $progress->label() }}</span>
        </div>
    </div>

    <div class="container-site mt-8 space-y-4">
        @foreach (['success' => 'border-green-200 bg-green-50 text-green-900', 'error' => 'border-red-200 bg-red-50 text-red-900', 'info' => 'border-ink-200 bg-white text-ink-900', 'warning' => 'border-brand-500 bg-brand-50 text-ink-900'] as $key => $style)
            @if (session($key))
                <div class="flex gap-3 rounded-lg border px-4 py-3 text-sm font-medium {{ $style }}" role="status"><x-site.icon :name="$key === 'error' ? 'x' : 'check-circle'" class="h-5 w-5 shrink-0" />{{ session($key) }}</div>
            @endif
        @endforeach
        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900" role="alert">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
        @endif
    </div>

    <div class="container-site mt-8 grid gap-8 lg:grid-cols-12">
        <div class="space-y-8 lg:col-span-8">
            {{-- Progress --}}
            <section class="rounded-lg border border-ink-100 bg-white p-6 shadow-sm sm:p-8" aria-labelledby="progress-title">
                <h2 id="progress-title" class="text-3xl">Your progress</h2>
                <ol class="mt-6">
                    @foreach ($progress->steps() as $i => $step)
                        <li class="relative flex gap-4 pb-8 last:pb-0">
                            @unless ($loop->last)
                                <span @class(['absolute left-[15px] top-9 h-[calc(100%-2.25rem)] w-0.5', 'bg-ink-950' => $step['state'] === 'done', 'bg-ink-100' => $step['state'] !== 'done']) aria-hidden="true"></span>
                            @endunless
                            <span @class([
                                'relative z-10 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-bold',
                                'bg-ink-950 text-brand-400' => $step['state'] === 'done',
                                'bg-brand-400 text-ink-950 ring-4 ring-brand-100' => $step['state'] === 'current',
                                'bg-ink-100 text-ink-400' => $step['state'] === 'upcoming',
                            ])>
                                @if ($step['state'] === 'done')<x-site.icon name="check" class="h-4 w-4" />@else{{ $i + 1 }}@endif
                            </span>
                            <div class="pt-0.5">
                                <p @class(['font-semibold', 'text-ink-950' => $step['state'] !== 'upcoming', 'text-ink-400' => $step['state'] === 'upcoming'])>
                                    {{ $step['title'] }}
                                    @if ($step['state'] === 'current')<span class="ml-2 rounded bg-brand-100 px-1.5 py-0.5 text-xs font-semibold uppercase tracking-wider text-ink-800">Next step</span>@endif
                                </p>
                                <p class="text-sm text-ink-500">{{ $step['detail'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </section>

            {{-- Courses --}}
            <section class="rounded-lg border border-ink-100 bg-white p-6 shadow-sm sm:p-8" x-data="{ editing: {{ $errors->has('selected_objective') ? 'true' : 'false' }} }" aria-labelledby="courses-title">
                <div class="flex items-center justify-between gap-4">
                    <h2 id="courses-title" class="text-3xl">Your courses</h2>
                    @unless ($isPaid)
                        <button type="button" class="text-sm font-semibold text-ink-600 underline decoration-brand-400 decoration-2 underline-offset-4 hover:text-ink-950" @click="editing = !editing" x-text="editing ? 'Cancel' : 'Change courses'">Change courses</button>
                    @endunless
                </div>

                <ul class="mt-5 divide-y divide-ink-100" x-show="!editing">
                    @forelse ($chosen as $course)
                        <li class="flex items-center gap-4 py-4">
                            @if ($course->image_url)<img src="{{ img($course->image_url, 200) }}" alt="" class="h-14 w-20 rounded object-cover">@endif
                            <div class="flex-1">
                                <a href="{{ route('course', $course->slug) }}" class="font-semibold text-ink-950 hover:underline">{{ $course->objective }}</a>
                                <p class="text-sm text-ink-500">{{ $course->duration }} weeks · {{ $course->theory_session }} theory, {{ $course->practical_session }} practical sessions</p>
                            </div>
                            <p class="font-display text-xl font-bold text-ink-950">{{ $currency }}{{ number_format($course->price) }}</p>
                        </li>
                    @empty
                        <li class="py-4 text-ink-500">No course selected.</li>
                    @endforelse
                </ul>

                @unless ($isPaid)
                    <form method="POST" action="{{ route('application.courses') }}" x-show="editing" x-cloak class="mt-5 space-y-3">
                        @csrf @method('PUT')
                        @foreach ($courses as $course)
                            <label class="flex cursor-pointer items-center gap-4 rounded-lg border-2 border-ink-100 p-4 has-[:checked]:border-ink-950 has-[:checked]:bg-brand-50">
                                <input type="checkbox" name="selected_objective[]" value="{{ $course->id }}" @checked(in_array($course->id, old('selected_objective', $chosenIds))) class="h-5 w-5 rounded border-ink-300 text-ink-950 focus:ring-brand-400">
                                <span class="flex-1 font-semibold text-ink-900">{{ $course->objective }} <span class="block text-sm font-normal text-ink-500">{{ $course->duration }} weeks</span></span>
                                <span class="font-display text-xl font-bold">{{ $currency }}{{ number_format($course->price) }}</span>
                            </label>
                        @endforeach
                        <button class="btn-dark btn-sm">Save selection</button>
                    </form>
                @endunless
            </section>

            {{-- Documents --}}
            <section class="rounded-lg border border-ink-100 bg-white p-6 shadow-sm sm:p-8" aria-labelledby="documents-title">
                <h2 id="documents-title" class="text-3xl">Documents</h2>
                <p class="mt-1 text-sm text-ink-500">Uploads are private and only visible to you and the admissions team.</p>

                <ul class="mt-5 divide-y divide-ink-100">
                    <li class="flex flex-wrap items-center gap-4 py-4">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-md bg-ink-50"><x-site.icon name="user" class="h-5 w-5" /></span>
                        <div class="flex-1">
                            <p class="font-semibold text-ink-900">Passport photograph <span class="text-xs font-normal text-ink-400">required</span></p>
                            <p class="text-sm {{ $user->profile_photo_path ? 'text-green-700' : 'text-red-600' }}">{{ $user->profile_photo_path ? 'Uploaded' : 'Missing' }}</p>
                        </div>
                        @include('application.partials.upload', ['type' => 'passport_photo', 'label' => $user->profile_photo_path ? 'Replace' : 'Upload', 'accept' => 'image/jpeg,image/png,image/webp'])
                    </li>
                    @foreach ($documentTypes as $type => $label)
                        @php $document = $user->documents->firstWhere('type', $type); @endphp
                        <li class="flex flex-wrap items-center gap-4 py-4">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-md bg-ink-50"><x-site.icon name="file-text" class="h-5 w-5" /></span>
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-ink-900">{{ $label }} <span class="text-xs font-normal text-ink-400">optional</span></p>
                                @if ($document)
                                    <p class="text-sm text-ink-500">
                                        <a href="{{ route('application.documents.show', $document) }}" target="_blank" class="underline">{{ \Illuminate\Support\Str::limit($document->original_name, 40) }}</a>
                                        · <span @class(['text-green-700' => $document->status === 'approved', 'text-red-600' => $document->status === 'rejected'])>{{ ucfirst($document->status === 'pending' ? 'awaiting review' : $document->status) }}</span>
                                        @if ($document->review_note)<span class="block text-xs">{{ $document->review_note }}</span>@endif
                                    </p>
                                @else
                                    <p class="text-sm text-ink-400">Not uploaded</p>
                                @endif
                            </div>
                            @include('application.partials.upload', ['type' => $type, 'label' => $document ? 'Replace' : 'Upload', 'accept' => 'application/pdf,image/jpeg,image/png,image/webp'])
                        </li>
                    @endforeach
                </ul>
            </section>
        </div>

        {{-- Payment / next action --}}
        <aside class="lg:col-span-4">
            <div class="sticky top-6 space-y-6">
                @if ($isPaid)
                    <div class="overflow-hidden rounded-lg border-2 border-ink-950 bg-white">
                        <div class="bg-ink-950 p-6 text-white">
                            <p class="text-sm font-semibold uppercase tracking-wider text-ink-300">Fees</p>
                            <p class="mt-1 flex items-center gap-2 font-display text-4xl font-bold text-brand-400"><x-site.icon name="check-circle" class="h-8 w-8" /> Paid</p>
                        </div>
                        <dl class="space-y-2 p-6 text-sm">
                            <div class="flex justify-between"><dt class="text-ink-500">Amount</dt><dd class="font-semibold">{{ $currency }}{{ number_format($subscription->total_amount, 2) }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-ink-500">Reference</dt><dd class="break-all font-mono text-xs">{{ $subscription->payment_reference }}</dd></div>
                            <div class="flex justify-between"><dt class="text-ink-500">Method</dt><dd>{{ $subscription->payment_method }}</dd></div>
                        </dl>
                        <div class="p-6 pt-0"><a href="{{ route('dashboard') }}" class="btn-brand w-full">Open student portal</a></div>
                    </div>
                @elseif ($subscription)
                    <div class="overflow-hidden rounded-lg border-2 border-ink-950 bg-white">
                        <div class="bg-ink-950 p-6 text-white">
                            <p class="text-sm font-semibold uppercase tracking-wider text-ink-300">Amount due</p>
                            <p class="mt-1 font-display text-5xl font-bold text-brand-400">{{ $currency }}{{ number_format($subscription->total_amount, 2) }}</p>
                        </div>
                        <dl class="space-y-2 p-6 text-sm">
                            <div class="flex justify-between"><dt class="text-ink-500">Tuition</dt><dd>{{ $currency }}{{ number_format($subscription->subtotal, 2) }}</dd></div>
                            @if ($subscription->discount > 0)
                                <div class="flex justify-between text-green-700"><dt>Combo discount</dt><dd>−{{ $currency }}{{ number_format($subscription->discount, 2) }}</dd></div>
                            @endif
                            <div class="flex justify-between"><dt class="text-ink-500">VAT ({{ \App\Support\Pricing::vatLabel() }}%)</dt><dd>{{ $currency }}{{ number_format($subscription->tax, 2) }}</dd></div>
                        </dl>
                        <div class="space-y-3 p-6 pt-0">
                            <form method="POST" action="{{ route('payment') }}" x-data="{ sending: false }" @submit="sending = true">
                                @csrf
                                <button class="btn-brand w-full" :disabled="sending">
                                    <span x-show="!sending">Pay securely now</span><span x-show="sending" x-cloak>Redirecting to Paystack…</span>
                                </button>
                            </form>
                            <p class="text-center text-xs text-ink-400">Card, bank transfer or USSD via Paystack. You'll return here automatically.</p>
                        </div>
                    </div>

                    <div class="rounded-lg bg-white p-6 text-sm shadow-sm ring-1 ring-ink-100">
                        <h3 class="text-xl">Paying another way?</h3>
                        <p class="mt-2 text-ink-500">If you paid at the school or by direct transfer, admissions will record it and your status updates here. Quote application <strong class="text-ink-900">#{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</strong>.</p>
                        <a href="{{ route('contact') }}" class="link-underline mt-3 inline-block">Contact admissions</a>
                    </div>
                @endif

                <div class="rounded-lg bg-white p-6 text-sm shadow-sm ring-1 ring-ink-100">
                    <h3 class="text-xl">Your details</h3>
                    <dl class="mt-3 space-y-1.5 text-ink-600">
                        <div><dt class="sr-only">Name</dt><dd class="font-semibold text-ink-900">{{ $user->full_name }}</dd></div>
                        <div><dt class="sr-only">Email</dt><dd class="break-all">{{ $user->email }}</dd></div>
                        <div><dt class="sr-only">Phone</dt><dd>{{ $user->mobileNumber }}</dd></div>
                        @if ($user->enrolmentBatch)<div><dt class="inline text-ink-400">Intake:</dt> <dd class="inline">{{ $user->enrolmentBatch->batch_name }}</dd></div>@endif
                    </dl>
                </div>
            </div>
        </aside>
    </div>
</x-site.auth>
