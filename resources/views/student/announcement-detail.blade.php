@section('title', $announcement->title)
<x-app-layout>
    <x-portal.page :title="$announcement->title" :back="route('student.announcements')">
        <article class="portal-card max-w-3xl">
            <div class="flex flex-wrap items-center gap-2 border-b border-ink-100 px-6 py-4 text-sm text-ink-500">
                @if ($announcement->priority === 'high')<span class="badge-red">Important</span>@endif
                <span class="badge-grey">{{ $announcement->course?->objective ?? 'All students' }}</span>
                <span>{{ ($announcement->publish_date ?? $announcement->created_at)->format('j F Y') }}</span>
                @if ($announcement->creator)<span>· from {{ $announcement->creator->firstName }} {{ $announcement->creator->lastName }}</span>@endif
            </div>
            <div class="px-6 py-6 text-base leading-relaxed text-ink-800">{!! nl2br(e($announcement->content)) !!}</div>
        </article>
    </x-portal.page>
</x-app-layout>
