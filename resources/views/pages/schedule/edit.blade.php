@section('title', 'Edit session')
@php $chosen = old('students', $schedule->studentIds()); @endphp
<x-app-layout>
    <x-portal.page title="Edit session" :subtitle="$schedule->course?->objective . ($schedule->batch ? ' · ' . $schedule->batch->batch_name : '')" :back="route('schedule')">
        <form method="POST" action="{{ route('schedule.update', $schedule->id) }}" class="grid gap-6 xl:grid-cols-3">
            @csrf @method('PUT')

            <section class="portal-card p-5 xl:col-span-2">
                <h2 class="portal-card-title">When &amp; what</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="portal-label" for="schedule_date">Date</label>
                        <input id="schedule_date" type="date" name="schedule_date" required class="portal-input" value="{{ old('schedule_date', $schedule->schedule_date->format('Y-m-d')) }}">
                    </div>
                    <div>
                        <label class="portal-label" for="time_start">Starts</label>
                        <input id="time_start" type="time" name="time_start" required class="portal-input" value="{{ old('time_start', \Carbon\Carbon::parse($schedule->time_start)->format('H:i')) }}">
                    </div>
                    <div>
                        <label class="portal-label" for="time_stop">Ends</label>
                        <input id="time_stop" type="time" name="time_stop" required class="portal-input" value="{{ old('time_stop', \Carbon\Carbon::parse($schedule->time_stop)->format('H:i')) }}">
                    </div>
                    <div>
                        <label class="portal-label" for="instructor_id">Instructor</label>
                        <select id="instructor_id" name="instructor_id" required class="portal-input">
                            @foreach ($instructors as $instructor)
                                <option value="{{ $instructor->id }}" @selected(old('instructor_id', $schedule->instructor_id) == $instructor->id)>{{ $instructor->firstName }} {{ $instructor->lastName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="portal-label" for="topic_id">Topic</label>
                        <select id="topic_id" name="topic_id" required class="portal-input">
                            @foreach ($topics as $topic)
                                <option value="{{ $topic->id }}" @selected(old('topic_id', $schedule->topic_id) == $topic->id)>{{ $topic->topic }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="portal-label" for="session_type">Session type</label>
                        <select id="session_type" name="session_type" class="portal-input">
                            @foreach (['theory' => 'Theory (classroom)', 'practical' => 'Practical (vehicle)'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('session_type', $schedule->session_type ?? 'theory') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <a href="{{ route('schedule') }}" class="btn-ghost btn-sm">Cancel</a>
                    <button class="btn-brand btn-sm">Save session</button>
                </div>
            </section>

            <section class="portal-card" x-data>
                <div class="portal-card-header">
                    <h2 class="portal-card-title">Students</h2>
                    <button type="button" class="text-sm font-semibold text-ink-600 hover:text-ink-950" @click="$root.querySelectorAll('input[type=checkbox]').forEach(c => c.checked = true)">Select all</button>
                </div>
                <ul class="max-h-[28rem] divide-y divide-ink-100 overflow-y-auto">
                    @forelse ($students as $student)
                        <li>
                            <label class="flex items-center gap-3 px-5 py-2.5 hover:bg-ink-50">
                                <input type="checkbox" name="students[]" value="{{ $student->id }}" @checked(in_array($student->id, $chosen)) class="rounded border-ink-300 text-ink-950 focus:ring-brand-400">
                                <x-portal.avatar :user="$student" size="h-7 w-7" />
                                <span class="text-sm font-medium text-ink-900">{{ $student->firstName }} {{ $student->lastName }}</span>
                            </label>
                        </li>
                    @empty
                        <li><x-portal.empty icon="graduation" title="No students on this course" text="Students appear once they're enrolled and assigned to this course." /></li>
                    @endforelse
                </ul>
            </section>
        </form>
    </x-portal.page>
</x-app-layout>
