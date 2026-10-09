@section('title', 'Schedule sessions')
@php
    $initial = [
        'course' => (string) old('course_id', $preselect['course'] ?? ''),
        'batch' => (string) old('batch_id', $preselect['batch'] ?? ''),
        'instructor' => (string) old('instructor_id', ''),
        'topic' => (string) old('topic_id', ''),
        'students' => array_map('intval', old('students', [])),
        'slots' => old('slots', [['date' => now()->addDay()->toDateString(), 'start' => '09:00', 'end' => '11:00']]),
    ];
@endphp
<x-app-layout>
    <x-portal.page title="Schedule sessions" subtitle="Pick the course, instructor and topic, then add one or more time slots. Each slot becomes a session in students' timetables." :back="route('schedule')">
        <form method="POST" action="{{ route('schedule.create') }}" class="grid gap-6 xl:grid-cols-3" x-data="scheduler(@js($initial))" x-init="init()">
            @csrf

            <div class="space-y-6 xl:col-span-2">
                {{-- 1. What --}}
                <section class="portal-card p-5">
                    <h2 class="portal-card-title"><span class="text-brand-600">1.</span> Course &amp; topic</h2>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="portal-label" for="course_id">Course</label>
                            <select id="course_id" name="course_id" required class="portal-input" x-model="course" @change="loadCourse()">
                                <option value="">Choose a course</option>
                                @foreach ($courses as $course)<option value="{{ $course->id }}">{{ $course->objective }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="portal-label" for="batch_id">Intake batch</label>
                            <select id="batch_id" name="batch_id" required class="portal-input" x-model="batch" @change="loadStudents()">
                                @foreach ($batches as $batch)<option value="{{ $batch->id }}">{{ $batch->batch_name }}{{ $batch->active_batch ? ' (current)' : '' }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="portal-label" for="topic_id">Curriculum topic</label>
                            <select id="topic_id" name="topic_id" required class="portal-input" x-model="topic" :disabled="!course">
                                <option value="" x-text="course ? (topics.length ? 'Choose a topic' : 'This course has no topics yet') : 'Choose a course first'"></option>
                                <template x-for="t in topics" :key="t.id"><option :value="t.id" x-text="t.topic" :selected="String(t.id) === topic"></option></template>
                            </select>
                            <p class="mt-1 text-xs text-ink-500" x-show="course && !topics.length && !loading" x-cloak>Add topics on the <a :href="'{{ url('course/details') }}/' + course + '?tab=curriculum'" class="underline">course page</a> first.</p>
                        </div>
                        <div>
                            <span class="portal-label">Session type</span>
                            <div class="flex gap-2">
                                @foreach (['theory' => 'Theory', 'practical' => 'Practical'] as $value => $label)
                                    <label class="flex-1">
                                        <input type="radio" name="session_type" value="{{ $value }}" class="peer sr-only" @checked(old('session_type', 'theory') === $value)>
                                        <span class="flex h-[38px] cursor-pointer items-center justify-center rounded-lg border border-ink-200 text-sm font-semibold text-ink-600 peer-checked:border-ink-950 peer-checked:bg-ink-950 peer-checked:text-white">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>

                {{-- 2. When --}}
                <section class="portal-card p-5">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="portal-card-title"><span class="text-brand-600">2.</span> Time slots</h2>
                        <div class="flex gap-2">
                            <button type="button" class="btn-ghost btn-xs" @click="repeatWeekly()" x-show="slots.length" title="Copy the last slot to the same time next week">+ Next week</button>
                            <button type="button" class="btn-dark btn-xs" @click="addSlot()"><x-site.icon name="plus" class="h-3.5 w-3.5" /> Add slot</button>
                        </div>
                    </div>
                    <ul class="mt-4 space-y-3">
                        <template x-for="(slot, i) in slots" :key="slot.key">
                            <li class="grid items-end gap-3 rounded-lg bg-ink-50 p-3 sm:grid-cols-[2rem_1fr_8rem_8rem_auto]">
                                <span class="font-display text-2xl font-bold text-ink-400" x-text="i + 1"></span>
                                <div>
                                    <label class="portal-label" :for="'date-' + i">Date</label>
                                    <input type="date" :id="'date-' + i" :name="'slots[' + i + '][date]'" x-model="slot.date" required min="{{ today()->toDateString() }}" class="portal-input">
                                </div>
                                <div>
                                    <label class="portal-label" :for="'start-' + i">Starts</label>
                                    <input type="time" :id="'start-' + i" :name="'slots[' + i + '][start]'" x-model="slot.start" required class="portal-input">
                                </div>
                                <div>
                                    <label class="portal-label" :for="'end-' + i">Ends</label>
                                    <input type="time" :id="'end-' + i" :name="'slots[' + i + '][end]'" x-model="slot.end" required class="portal-input">
                                </div>
                                <button type="button" class="btn-ghost btn-xs text-red-600" @click="slots.splice(i, 1)" x-show="slots.length > 1" aria-label="Remove slot"><x-site.icon name="trash" class="h-4 w-4" /></button>
                            </li>
                        </template>
                    </ul>
                </section>
            </div>

            {{-- 3. Who --}}
            <aside class="space-y-6">
                <section class="portal-card p-5">
                    <h2 class="portal-card-title"><span class="text-brand-600">3.</span> Instructor</h2>
                    <select name="instructor_id" required class="portal-input mt-4" x-model="instructor" @change="loadStudents()" :disabled="!course" aria-label="Instructor">
                        <option value="" x-text="course ? (instructors.length ? 'Choose an instructor' : 'No instructor teaches this course') : 'Choose a course first'"></option>
                        <template x-for="p in instructors" :key="p.id"><option :value="p.id" x-text="p.firstName + ' ' + p.lastName" :selected="String(p.id) === instructor"></option></template>
                    </select>
                    <p class="mt-2 text-xs text-ink-500">Only instructors whose role teaches this course are listed (set under Roles &amp; permissions).</p>
                </section>

                <section class="portal-card">
                    <div class="portal-card-header">
                        <h2 class="portal-card-title"><span class="text-brand-600">4.</span> Students</h2>
                        <button type="button" class="text-sm font-semibold text-ink-600 hover:text-ink-950" x-show="students.length" @click="selected = selected.length === students.length ? [] : students.map(s => s.id)" x-text="selected.length === students.length ? 'Clear' : 'Select all'"></button>
                    </div>
                    <ul class="max-h-80 divide-y divide-ink-100 overflow-y-auto">
                        <template x-for="s in students" :key="s.id">
                            <li>
                                <label class="flex items-center gap-3 px-5 py-2.5 hover:bg-ink-50">
                                    <input type="checkbox" name="students[]" :value="s.id" x-model.number="selected" class="rounded border-ink-300 text-ink-950 focus:ring-brand-400">
                                    <span class="text-sm font-medium text-ink-900" x-text="s.firstName + ' ' + s.lastName"></span>
                                </label>
                            </li>
                        </template>
                        <li x-show="!students.length" class="px-5 py-6 text-center text-sm text-ink-500" x-text="instructor ? 'This instructor has no students on this course in this batch.' : 'Choose a course, batch and instructor to see their students.'"></li>
                    </ul>
                    <div class="space-y-2 border-t border-ink-100 p-5 text-sm" x-show="slots.length > 1" x-cloak>
                        <p class="portal-label">With several slots</p>
                        <label class="flex gap-2"><input type="radio" name="allocation" value="all" x-model="allocation" class="mt-0.5 border-ink-300 text-ink-950 focus:ring-brand-400"> Every selected student attends every slot</label>
                        <label class="flex gap-2"><input type="radio" name="allocation" value="split" x-model="allocation" class="mt-0.5 border-ink-300 text-ink-950 focus:ring-brand-400"> Share students out across the slots (e.g. practical groups)</label>
                        <p class="text-xs text-ink-500" x-show="allocation === 'split' && selected.length" x-text="'About ' + Math.ceil(selected.length / slots.length) + ' student(s) per slot'"></p>
                    </div>
                </section>

                <button class="btn-brand w-full" x-text="'Create ' + slots.length + ' ' + (slots.length === 1 ? 'session' : 'sessions')">Create sessions</button>
            </aside>
        </form>
    </x-portal.page>

    @push('scripts')
        <script>
            function scheduler(initial) {
                const base = '{{ url('training-schedule') }}';
                let nextKey = 0;
                return {
                    course: initial.course, batch: initial.batch, instructor: initial.instructor, topic: initial.topic,
                    topics: [], instructors: [], students: [], selected: initial.students,
                    allocation: @js(old('allocation', 'all')), loading: false,
                    slots: initial.slots.map(s => ({ ...s, key: nextKey++ })),
                    async init() { if (this.course) { await this.loadCourse(true); await this.loadStudents(true); } },
                    async loadCourse(keep = false) {
                        if (!keep) { this.topic = ''; this.instructor = ''; this.students = []; this.selected = []; }
                        if (!this.course) { this.topics = []; this.instructors = []; return; }
                        this.loading = true;
                        const [topics, instructors] = await Promise.all([
                            fetch(`${base}/topics/${this.course}`).then(r => r.json()),
                            fetch(`${base}/instructors/${this.course}`).then(r => r.json()),
                        ]);
                        this.topics = topics; this.instructors = instructors; this.loading = false;
                        if (!keep && instructors.length === 1) { this.instructor = String(instructors[0].id); this.loadStudents(); }
                    },
                    async loadStudents(keep = false) {
                        if (!keep) this.selected = [];
                        if (!this.course || !this.batch || !this.instructor) { this.students = []; return; }
                        this.students = await fetch(`${base}/instructor-students/${this.batch}/${this.course}/${this.instructor}`).then(r => r.json());
                        if (!keep) this.selected = this.students.map(s => s.id);
                    },
                    addSlot() {
                        const last = this.slots[this.slots.length - 1];
                        this.slots.push({ date: last ? last.date : '', start: last ? last.end : '09:00', end: '', key: nextKey++ });
                    },
                    repeatWeekly() {
                        const last = this.slots[this.slots.length - 1];
                        // Local-date arithmetic (toISOString would shift the day in UTC+1)
                        const [y, m, day] = last.date.split('-').map(Number);
                        const d = new Date(y, m - 1, day + 7);
                        const iso = [d.getFullYear(), String(d.getMonth() + 1).padStart(2, '0'), String(d.getDate()).padStart(2, '0')].join('-');
                        this.slots.push({ date: iso, start: last.start, end: last.end, key: nextKey++ });
                    },
                };
            }
        </script>
    @endpush
</x-app-layout>
