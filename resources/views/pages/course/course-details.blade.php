@section('title', $course->objective)
@php
    $currency = $settings->base_currency ?? '₦';
    $canEdit = auth()->user()->hasPermission('update_course_management');
    $tab = request('tab', $errors->has('files') || $errors->has('files.*') ? 'materials' : ($errors->hasAny(['topic', 'summary']) ? 'curriculum' : 'overview'));
    $tabs = [
        'overview' => 'Overview',
        'curriculum' => 'Curriculum (' . $curriculum->count() . ')',
        'materials' => 'Materials (' . $materials->count() . ')',
        'students' => 'Students (' . $students->count() . ')',
        'sessions' => 'Upcoming sessions (' . $sessions->count() . ')',
    ];
@endphp
<x-app-layout>
    <x-portal.page :title="$course->objective" :back="route('course-management')">
        <x-slot:actions>
            <a href="{{ route('course', $course->slug) }}" target="_blank" class="btn-ghost btn-sm"><x-site.icon name="external" class="h-4 w-4" /> View on website</a>
            @can('manage_course_catalogue')
                <a href="{{ route('objectives') }}" class="btn-ghost btn-sm"><x-site.icon name="pencil" class="h-4 w-4" /> Price &amp; details</a>
            @endcan
        </x-slot:actions>

        {{-- Summary strip --}}
        <section class="portal-card mb-6 flex flex-col overflow-hidden sm:flex-row">
            @if ($course->image_url)
                <img src="{{ img($course->image_url, 600) }}" alt="" class="h-40 w-full object-cover sm:h-auto sm:w-64">
            @endif
            <dl class="grid flex-1 grid-cols-2 gap-4 p-5 md:grid-cols-3 xl:grid-cols-6">
                @foreach ([
                    'Tuition' => $currency . number_format($course->price),
                    'Duration' => $course->duration . ' weeks',
                    'Theory' => ($course->theory_session ?: 0) . ' sessions',
                    'Practical' => ($course->practical_session ?: 0) . ' sessions',
                    'Examination' => $course->examination ?: '—',
                    'Students' => $students->count(),
                ] as $label => $value)
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-ink-400">{{ $label }}</dt>
                        <dd class="mt-1 font-display text-2xl font-bold text-ink-950">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </section>

        <div x-data="{ tab: @js($tab) }">
            <nav class="mb-6 flex gap-1 overflow-x-auto rounded-xl bg-white p-1 shadow-sm ring-1 ring-ink-100" role="tablist">
                @foreach ($tabs as $key => $label)
                    <button type="button" role="tab" @click="tab = '{{ $key }}'; history.replaceState(null, '', '?tab={{ $key }}')"
                            :aria-selected="(tab === '{{ $key }}').toString()"
                            :class="tab === '{{ $key }}' ? 'bg-ink-950 text-white' : 'text-ink-600 hover:bg-ink-50'"
                            class="shrink-0 rounded-lg px-4 py-2 text-sm font-semibold transition">{{ $label }}</button>
                @endforeach
            </nav>

            {{-- Overview --}}
            <section x-show="tab === 'overview'" @if ($tab !== 'overview') x-cloak @endif class="grid gap-6 xl:grid-cols-3">
                <div class="portal-card xl:col-span-2" x-data="{ editing: false }">
                    <div class="portal-card-header">
                        <h2 class="portal-card-title">Course description</h2>
                        @if ($canEdit)
                            <button type="button" class="btn-ghost btn-xs" @click="editing = !editing" x-text="editing ? 'Cancel' : 'Edit'">Edit</button>
                        @endif
                    </div>
                    <div class="prose prose-sm max-w-none p-5 text-ink-700" x-show="!editing">
                        {!! clean_html($course->course_details) ?: '<p class="text-ink-400">No description yet.</p>' !!}
                    </div>
                    @if ($canEdit)
                        <form method="POST" action="{{ route('course-details.update') }}" class="p-5" x-show="editing" x-cloak>
                            @csrf @method('PUT')
                            <input type="hidden" name="id" value="{{ $course->id }}">
                            <label for="course_details" class="sr-only">Course description</label>
                            <textarea id="course_details" name="course_details" class="rich-editor" rows="12">{{ clean_html($course->course_details) }}</textarea>
                            <p class="mt-2 text-xs text-ink-500">Shown on the public course page.</p>
                            <div class="mt-4 flex justify-end"><button class="btn-brand btn-sm">Save description</button></div>
                        </form>
                    @endif
                </div>

                <div class="portal-card self-start">
                    <div class="portal-card-header"><h2 class="portal-card-title">Entry requirements</h2></div>
                    <ul class="space-y-2 p-5 text-sm text-ink-700">
                        @forelse ($course->requirementList() as $requirement)
                            <li class="flex gap-2"><x-site.icon name="check-circle" class="mt-0.5 h-4 w-4 shrink-0 text-ink-950" />{{ $requirement }}</li>
                        @empty
                            <li class="text-ink-400">None listed.</li>
                        @endforelse
                    </ul>
                    @can('manage_course_catalogue')
                        <p class="border-t border-ink-100 px-5 py-3 text-xs text-ink-500">Edit requirements under <a href="{{ route('objectives') }}" class="underline">Courses &amp; fees</a>.</p>
                    @endcan
                </div>
            </section>

            {{-- Curriculum --}}
            <section x-show="tab === 'curriculum'" @if ($tab !== 'curriculum') x-cloak @endif class="grid gap-6 xl:grid-cols-3">
                <div class="portal-card xl:col-span-2">
                    <div class="portal-card-header">
                        <h2 class="portal-card-title">Topics</h2>
                        <span class="text-xs text-ink-400">Sessions are scheduled against these topics</span>
                    </div>
                    @if ($curriculum->isEmpty())
                        <x-portal.empty icon="clipboard" title="No topics yet" text="Add the topics this course covers. Training sessions are scheduled against them." />
                    @else
                        <ol class="divide-y divide-ink-100">
                            @foreach ($curriculum as $n => $topic)
                                <li class="p-5" x-data="{ editing: false }">
                                    <div class="flex gap-4" x-show="!editing">
                                        <span class="font-display text-3xl font-extrabold leading-none text-brand-600">{{ str_pad($n + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                        <div class="min-w-0 flex-1">
                                            <p class="font-semibold text-ink-950">{{ $topic->topic }}</p>
                                            @if ($topic->summary)<p class="mt-1 text-sm text-ink-500">{{ strip_tags($topic->summary) }}</p>@endif
                                            <p class="mt-1 text-xs text-ink-400">{{ $topicSessions[$topic->id] ?? 0 }} {{ \Illuminate\Support\Str::plural('session', $topicSessions[$topic->id] ?? 0) }} scheduled</p>
                                        </div>
                                        @if ($canEdit)
                                            <div class="flex shrink-0 items-start gap-1">
                                                <button type="button" class="btn-ghost btn-xs" @click="editing = true" aria-label="Edit {{ $topic->topic }}"><x-site.icon name="pencil" class="h-4 w-4" /></button>
                                                <form method="POST" action="{{ route('curriculum.destroy', $topic->id) }}" onsubmit="return confirm(@js('Delete the topic “' . $topic->topic . '”?'))">
                                                    @csrf @method('DELETE')
                                                    <button class="btn-ghost btn-xs text-red-600" aria-label="Delete {{ $topic->topic }}"><x-site.icon name="trash" class="h-4 w-4" /></button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                    @if ($canEdit)
                                        <form method="POST" action="{{ route('curriculum.update', $topic->id) }}" class="space-y-3" x-show="editing" x-cloak>
                                            @csrf @method('PUT')
                                            <div>
                                                <label class="portal-label" for="topic-{{ $topic->id }}">Topic</label>
                                                <input id="topic-{{ $topic->id }}" name="topic" value="{{ $topic->topic }}" required maxlength="255" class="portal-input">
                                            </div>
                                            <div>
                                                <label class="portal-label" for="summary-{{ $topic->id }}">Summary</label>
                                                <textarea id="summary-{{ $topic->id }}" name="summary" rows="3" required class="portal-input">{{ strip_tags($topic->summary) }}</textarea>
                                            </div>
                                            <div class="flex justify-end gap-2">
                                                <button type="button" class="btn-ghost btn-sm" @click="editing = false">Cancel</button>
                                                <button class="btn-dark btn-sm">Save topic</button>
                                            </div>
                                        </form>
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </div>

                @if ($canEdit)
                    <form method="POST" action="{{ route('curriculum.store') }}" class="portal-card self-start p-5">
                        @csrf
                        <input type="hidden" name="objective_id" value="{{ $course->id }}">
                        <h2 class="portal-card-title">Add a topic</h2>
                        <div class="mt-4 space-y-3">
                            <div>
                                <label class="portal-label" for="new-topic">Topic</label>
                                <input id="new-topic" name="topic" value="{{ old('topic') }}" required maxlength="255" class="portal-input" placeholder="e.g. Pre-trip inspection">
                            </div>
                            <div>
                                <label class="portal-label" for="new-summary">Summary</label>
                                <textarea id="new-summary" name="summary" rows="4" required class="portal-input" placeholder="What students learn in this topic">{{ old('summary') }}</textarea>
                            </div>
                            <button class="btn-brand btn-sm w-full">Add topic</button>
                        </div>
                    </form>
                @endif
            </section>

            {{-- Materials --}}
            <section x-show="tab === 'materials'" @if ($tab !== 'materials') x-cloak @endif class="grid gap-6 xl:grid-cols-3">
                <div class="portal-card xl:col-span-2">
                    <div class="portal-card-header">
                        <h2 class="portal-card-title">Materials</h2>
                        <span class="text-xs text-ink-400">Enrolled students can download these</span>
                    </div>
                    @if ($materials->isEmpty())
                        <x-portal.empty icon="book" title="No materials yet" text="Upload handbooks, slides and worksheets for students on this course." />
                    @else
                        <ul class="divide-y divide-ink-100">
                            @foreach ($materials as $material)
                                @php $ext = $material->file_type ?: pathinfo($material->file_url, PATHINFO_EXTENSION); @endphp
                                <li class="flex items-center gap-4 px-5 py-3">
                                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-ink-950 text-[10px] font-bold uppercase text-brand-400">{{ \Illuminate\Support\Str::limit($ext, 4, '') }}</span>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate font-semibold text-ink-950">{{ $material->file_name }}</p>
                                        <p class="truncate text-xs text-ink-500">
                                            {{ $material->created_at?->format('j M Y') }}
                                            @if ($material->file_size) · {{ $material->file_size }}@endif
                                            @if ($material->uploadedBy) · {{ $material->uploadedBy->firstName }} {{ $material->uploadedBy->lastName }}@endif
                                        </p>
                                        @if ($material->description)<p class="truncate text-xs text-ink-400">{{ $material->description }}</p>@endif
                                    </div>
                                    <a href="{{ route('materials.download', $material->id) }}" class="btn-ghost btn-xs" aria-label="Download {{ $material->file_name }}"><x-site.icon name="download" class="h-4 w-4" /></a>
                                    @if ($canEdit)
                                        <form method="POST" action="{{ route('material.delete', $material->id) }}" onsubmit="return confirm(@js('Delete “' . $material->file_name . '”? Students will no longer be able to download it.'))">
                                            @csrf @method('DELETE')
                                            <button class="btn-ghost btn-xs text-red-600" aria-label="Delete {{ $material->file_name }}"><x-site.icon name="trash" class="h-4 w-4" /></button>
                                        </form>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                @if ($canEdit)
                    <form method="POST" action="{{ route('upload-course-materials') }}" enctype="multipart/form-data" class="portal-card self-start p-5" x-data="{ count: 0, sending: false }" @submit="sending = true">
                        @csrf
                        <input type="hidden" name="id" value="{{ $course->id }}">
                        <h2 class="portal-card-title">Upload materials</h2>
                        <div class="mt-4 space-y-3">
                            <label class="flex cursor-pointer flex-col items-center rounded-lg border-2 border-dashed border-ink-200 px-4 py-6 text-center hover:border-ink-400">
                                <x-site.icon name="download" class="h-6 w-6 rotate-180 text-ink-400" />
                                <span class="mt-2 text-sm font-semibold text-ink-900" x-text="count ? count + ' file(s) selected' : 'Choose files'">Choose files</span>
                                <span class="text-xs text-ink-400">PDF, Word, PowerPoint, Excel or images · up to 20 MB each</span>
                                <input type="file" name="files[]" multiple required class="sr-only" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png" @change="count = $event.target.files.length">
                            </label>
                            <div>
                                <label class="portal-label" for="description">Note for students (optional)</label>
                                <input id="description" name="description" maxlength="500" class="portal-input" placeholder="e.g. Read before week 2">
                            </div>
                            <button class="btn-brand btn-sm w-full" :disabled="sending"><span x-show="!sending">Upload</span><span x-show="sending" x-cloak>Uploading…</span></button>
                        </div>
                    </form>
                @endif
            </section>

            {{-- Students --}}
            <section x-show="tab === 'students'" @if ($tab !== 'students') x-cloak @endif class="portal-card">
                @if ($students->isEmpty())
                    <x-portal.empty icon="graduation" title="No students yet" text="Students appear here once they've paid and been assigned an instructor." />
                @else
                    <div class="overflow-x-auto">
                        <table class="portal-table">
                            <thead><tr><th>Student</th><th>Instructor</th><th>Batch</th><th>Contact</th></tr></thead>
                            <tbody>
                                @foreach ($students as $row)
                                    <tr>
                                        <td>
                                            @php $canOpen = auth()->user()->hasAnyPermission(['read_student_accounts', 'read_applicants']); @endphp
                                            <{{ $canOpen ? 'a' : 'span' }} @if ($canOpen) href="{{ route('admin.admissions.show', $row->student) }}" @endif class="flex items-center gap-3 font-semibold text-ink-950 {{ $canOpen ? 'hover:underline' : '' }}">
                                                <x-portal.avatar :user="$row->student" size="h-8 w-8" /> {{ $row->student->firstName }} {{ $row->student->lastName }}
                                            </{{ $canOpen ? 'a' : 'span' }}>
                                        </td>
                                        <td>{{ $row->instructor ? $row->instructor->firstName . ' ' . $row->instructor->lastName : '—' }}</td>
                                        <td>{{ $row->batch?->batch_name ?? '—' }}</td>
                                        <td class="text-ink-500">{{ $row->student->mobileNumber }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            {{-- Sessions --}}
            <section x-show="tab === 'sessions'" @if ($tab !== 'sessions') x-cloak @endif class="portal-card">
                <div class="portal-card-header">
                    <h2 class="portal-card-title">Upcoming sessions</h2>
                    @can('create_training_schedule')<a href="{{ route('schedule.new', ['course' => $course->id]) }}" class="btn-dark btn-sm"><x-site.icon name="plus" class="h-4 w-4" /> Schedule sessions</a>@endcan
                </div>
                @if ($sessions->isEmpty())
                    <x-portal.empty icon="calendar" title="No upcoming sessions" />
                @else
                    <div class="overflow-x-auto">
                        <table class="portal-table">
                            <thead><tr><th>Date</th><th>Time</th><th>Topic</th><th>Type</th><th>Instructor</th><th class="text-right">Students</th></tr></thead>
                            <tbody>
                                @foreach ($sessions as $session)
                                    <tr>
                                        <td class="whitespace-nowrap font-semibold text-ink-950">{{ $session->schedule_date->format('D j M') }}</td>
                                        <td class="whitespace-nowrap">{{ \Carbon\Carbon::parse($session->time_start)->format('g:ia') }}–{{ \Carbon\Carbon::parse($session->time_stop)->format('g:ia') }}</td>
                                        <td>{{ $session->topic?->topic ?? '—' }}</td>
                                        <td><span class="badge-grey">{{ ucfirst($session->session_type ?? 'theory') }}</span></td>
                                        <td>{{ $session->instructor?->firstName }} {{ $session->instructor?->lastName }}</td>
                                        <td class="text-right">{{ count($session->studentIds()) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>
    </x-portal.page>
</x-app-layout>
