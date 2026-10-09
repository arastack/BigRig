{{-- Shared create/edit fields. Expects $announcement (nullable), $courses, $batches, $prefix --}}
@php
    $a = $announcement;
    $value = fn ($field, $default = null) => $a ? $a->{$field} : old($field, $default);
    $dt = fn ($date) => $date ? \Illuminate\Support\Carbon::parse($date)->format('Y-m-d\TH:i') : '';
@endphp
<div class="space-y-4">
    <div>
        <label class="portal-label" for="{{ $prefix }}title">Title</label>
        <input id="{{ $prefix }}title" name="title" value="{{ $value('title') }}" required maxlength="255" class="portal-input" placeholder="e.g. School closed on Monday">
    </div>
    <div>
        <label class="portal-label" for="{{ $prefix }}content">Message</label>
        <textarea id="{{ $prefix }}content" name="content" rows="5" required maxlength="5000" class="portal-input">{{ $value('content') }}</textarea>
        <p class="mt-1 text-xs text-ink-400">Plain text. Line breaks are kept.</p>
    </div>

    <fieldset class="rounded-lg border border-ink-100 p-4">
        <legend class="px-1 text-sm font-semibold text-ink-900">Who should see it?</legend>
        <p class="mb-3 text-xs text-ink-500">Leave both as “Everyone” to reach every enrolled student. Pick a course and/or batch to narrow it down.</p>
        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <label class="portal-label" for="{{ $prefix }}course_id">Course</label>
                <select id="{{ $prefix }}course_id" name="course_id" class="portal-input">
                    <option value="">Everyone (all courses)</option>
                    @foreach ($courses as $course)<option value="{{ $course->id }}" @selected($value('course_id') == $course->id)>{{ $course->objective }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="portal-label" for="{{ $prefix }}batch_id">Batch</label>
                <select id="{{ $prefix }}batch_id" name="batch_id" class="portal-input">
                    <option value="">Everyone (all batches)</option>
                    @foreach ($batches as $batch)<option value="{{ $batch->id }}" @selected($value('batch_id') == $batch->id)>{{ $batch->batch_name }}{{ $batch->active_batch ? ' (current)' : '' }}</option>@endforeach
                </select>
            </div>
        </div>
    </fieldset>

    <div class="grid gap-3 sm:grid-cols-2">
        <div>
            <label class="portal-label" for="{{ $prefix }}priority">Priority</label>
            <select id="{{ $prefix }}priority" name="priority" class="portal-input">
                @foreach (['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'] as $key => $label)<option value="{{ $key }}" @selected($value('priority', 'medium') === $key)>{{ $label }}</option>@endforeach
            </select>
        </div>
        <label class="flex items-end gap-2 pb-2 text-sm">
            <input type="checkbox" name="urgent" value="1" @checked($a ? $a->type === 'urgent' : old('urgent')) class="mb-0.5 rounded border-ink-300 text-ink-950 focus:ring-brand-400">
            <span>Mark as urgent</span>
        </label>
    </div>

    <div class="grid gap-3 sm:grid-cols-2">
        <div>
            <label class="portal-label" for="{{ $prefix }}publish_date">Show from <span class="normal-case tracking-normal text-ink-400">(optional)</span></label>
            <input id="{{ $prefix }}publish_date" type="datetime-local" name="publish_date" value="{{ $a ? $dt($a->publish_date) : old('publish_date') }}" class="portal-input">
        </div>
        <div>
            <label class="portal-label" for="{{ $prefix }}expiry_date">Show until <span class="normal-case tracking-normal text-ink-400">(optional)</span></label>
            <input id="{{ $prefix }}expiry_date" type="datetime-local" name="expiry_date" value="{{ $a ? $dt($a->expiry_date) : old('expiry_date') }}" class="portal-input">
        </div>
    </div>
    <p class="-mt-2 text-xs text-ink-400">Leave “Show from” empty to publish straight away, and “Show until” empty to keep it up until you turn it off.</p>

    <label class="flex gap-2 text-sm">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" @checked($a ? $a->is_active : old('is_active', true)) class="mt-0.5 rounded border-ink-300 text-ink-950 focus:ring-brand-400">
        <span>Turned on (students can see it during the dates above)</span>
    </label>
</div>
