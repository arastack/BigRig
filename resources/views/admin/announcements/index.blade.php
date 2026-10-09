@section('title', 'Announcements')
@php
    $stateOf = function ($a) {
        if (! $a->is_active) return ['Turned off', 'badge-grey'];
        if ($a->publish_date?->isFuture()) return ['Scheduled', 'badge-yellow'];
        if ($a->expiry_date?->isPast()) return ['Expired', 'badge-grey'];
        return ['Live', 'badge-green'];
    };
    $activeFilters = array_filter(\Illuminate\Support\Arr::except($filters, 'status'), fn ($v) => $v !== null && $v !== '');
@endphp
<x-app-layout>
    <x-portal.page title="Announcements" subtitle="Notices for students across the school. Instructors' course announcements appear here too.">
        <x-slot:actions>
            <button type="button" class="btn-brand btn-sm" @click="$dispatch('open-modal', 'announcement-create')"><x-site.icon name="plus" class="h-4 w-4" /> New announcement</button>
        </x-slot:actions>

        {{-- Status tabs --}}
        <nav class="mb-6 flex gap-1 overflow-x-auto rounded-xl bg-white p-1 shadow-sm ring-1 ring-ink-100" aria-label="Status">
            @php $current = $filters['status'] ?? ''; $current = array_key_exists($current, $statuses) ? $current : ''; @endphp
            @foreach (['' => 'All'] + $statuses as $key => $label)
                <a href="{{ route('admin.announcements.index', array_merge($activeFilters, $key ? ['status' => $key] : [])) }}"
                   @class(['flex shrink-0 items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition', 'bg-ink-950 text-white' => $current === $key, 'text-ink-600 hover:bg-ink-50' => $current !== $key])
                   @if ($current === $key) aria-current="page" @endif>
                    {{ $label }}
                    <span @class(['rounded-full px-2 py-0.5 text-xs', 'bg-brand-400 text-ink-950' => $current === $key, 'bg-ink-100 text-ink-600' => $current !== $key])>{{ $key ? $counts[$key] : $counts->sum() }}</span>
                </a>
            @endforeach
        </nav>

        <div class="portal-card">
            {{-- Filters --}}
            <form method="GET" class="grid gap-3 border-b border-ink-100 p-4 sm:grid-cols-2 lg:grid-cols-[1fr_14rem_12rem_auto]">
                @if ($current)<input type="hidden" name="status" value="{{ $current }}">@endif
                <label class="relative">
                    <span class="sr-only">Search</span>
                    <x-site.icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-400" />
                    <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search title or message" class="portal-input pl-9">
                </label>
                <label>
                    <span class="sr-only">Course</span>
                    <select name="course" class="portal-input">
                        <option value="">Any course</option>
                        <option value="none" @selected(($filters['course'] ?? null) === 'none')>All courses (school-wide)</option>
                        @foreach ($courses as $course)<option value="{{ $course->id }}" @selected(($filters['course'] ?? null) == $course->id)>{{ $course->objective }}</option>@endforeach
                    </select>
                </label>
                <label>
                    <span class="sr-only">Batch</span>
                    <select name="batch" class="portal-input">
                        <option value="">Any batch</option>
                        <option value="none" @selected(($filters['batch'] ?? null) === 'none')>All batches</option>
                        @foreach ($batches as $batch)<option value="{{ $batch->id }}" @selected(($filters['batch'] ?? null) == $batch->id)>{{ $batch->batch_name }}</option>@endforeach
                    </select>
                </label>
                <div class="flex gap-2">
                    <button class="btn-dark btn-sm flex-1"><x-site.icon name="filter" class="h-4 w-4" /> Filter</button>
                    @if ($activeFilters)
                        <a href="{{ route('admin.announcements.index', $current ? ['status' => $current] : []) }}" class="btn-ghost btn-sm">Clear</a>
                    @endif
                </div>
            </form>

            @if ($announcements->isEmpty())
                <x-portal.empty icon="megaphone" title="No announcements here" :text="$activeFilters || $current ? 'Nothing matches these filters.' : 'Post a notice and it will appear on students\' dashboards.'" />
            @else
                <ul class="divide-y divide-ink-100">
                    @foreach ($announcements as $announcement)
                        @php [$stateLabel, $stateClass] = $stateOf($announcement); @endphp
                        <li class="flex flex-col gap-4 p-5 lg:flex-row lg:items-start" x-data="{ more: false }">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="font-semibold text-ink-950">{{ $announcement->title }}</h2>
                                    <span class="{{ $stateClass }}">{{ $stateLabel }}</span>
                                    @if ($announcement->type === 'urgent')<span class="badge-red">Urgent</span>@endif
                                    @if ($announcement->priority === 'high')<span class="badge-dark">High priority</span>@elseif ($announcement->priority === 'low')<span class="badge-grey">Low priority</span>@endif
                                </div>
                                <div class="mt-1 text-sm text-ink-700" :class="more ? '' : 'line-clamp-2'">{!! nl2br(e($announcement->content)) !!}</div>
                                @if (mb_strlen($announcement->content) > 160 || substr_count($announcement->content, "\n") > 1)
                                    <button type="button" class="mt-1 text-xs font-semibold text-ink-500 hover:text-ink-950" @click="more = !more" x-text="more ? 'Show less' : 'Show more'">Show more</button>
                                @endif
                                <dl class="mt-3 flex flex-wrap gap-x-5 gap-y-1 text-xs text-ink-500">
                                    <div class="flex items-center gap-1"><x-site.icon name="users" class="h-3.5 w-3.5" /><dt class="sr-only">Audience</dt>
                                        <dd>{{ collect([$announcement->course?->objective ?? 'All courses', $announcement->batch?->batch_name ?? 'all batches'])->join(' · ') }}</dd></div>
                                    <div class="flex items-center gap-1"><x-site.icon name="user" class="h-3.5 w-3.5" /><dt class="sr-only">Author</dt>
                                        <dd>{{ $announcement->creator ? $announcement->creator->firstName . ' ' . $announcement->creator->lastName : 'Unknown' }}</dd></div>
                                    <div class="flex items-center gap-1"><x-site.icon name="calendar" class="h-3.5 w-3.5" /><dt class="sr-only">Dates</dt>
                                        <dd>
                                            {{ ($announcement->publish_date ?? $announcement->created_at)?->format('j M Y') }}
                                            @if ($announcement->expiry_date) – {{ $announcement->expiry_date->format('j M Y') }}@endif
                                        </dd></div>
                                    <div class="flex items-center gap-1"><x-site.icon name="eye" class="h-3.5 w-3.5" /><dt class="sr-only">Reads</dt>
                                        <dd>Read by {{ number_format($announcement->reads_count) }} {{ Str::plural('student', $announcement->reads_count) }}</dd></div>
                                </dl>
                            </div>
                            <div class="flex shrink-0 flex-wrap items-center gap-2">
                                <form method="POST" action="{{ route('admin.announcements.toggle', $announcement) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn-ghost btn-xs">{{ $announcement->is_active ? 'Turn off' : 'Turn on' }}</button>
                                </form>
                                <button type="button" class="btn-ghost btn-xs" @click="$dispatch('open-modal', 'announcement-edit-{{ $announcement->id }}')" aria-label="Edit {{ $announcement->title }}"><x-site.icon name="pencil" class="h-4 w-4" /></button>
                                <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}" onsubmit="return confirm('Delete this announcement? Students will no longer see it and this cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button class="btn-ghost btn-xs text-red-600" aria-label="Delete {{ $announcement->title }}"><x-site.icon name="trash" class="h-4 w-4" /></button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
                @if ($announcements->hasPages())
                    <div class="border-t border-ink-100 px-4 py-3">{{ $announcements->links() }}</div>
                @endif
            @endif
        </div>

        {{-- Create --}}
        <x-portal.modal name="announcement-create" title="New announcement" max-width="max-w-2xl">
            <form method="POST" action="{{ route('admin.announcements.store') }}">
                @csrf
                @include('admin.announcements._form', ['announcement' => null, 'prefix' => 'new_'])
                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" class="btn-ghost btn-sm" @click="$dispatch('close-modal')">Cancel</button>
                    <button class="btn-brand btn-sm">Post announcement</button>
                </div>
            </form>
        </x-portal.modal>

        {{-- Edit, one per announcement on this page --}}
        @foreach ($announcements as $announcement)
            <x-portal.modal :name="'announcement-edit-' . $announcement->id" title="Edit announcement" max-width="max-w-2xl">
                <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}">
                    @csrf @method('PUT')
                    @include('admin.announcements._form', ['announcement' => $announcement, 'prefix' => 'edit_' . $announcement->id . '_'])
                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" class="btn-ghost btn-sm" @click="$dispatch('close-modal')">Cancel</button>
                        <button class="btn-dark btn-sm">Save changes</button>
                    </div>
                </form>
            </x-portal.modal>
        @endforeach
    </x-portal.page>
</x-app-layout>
