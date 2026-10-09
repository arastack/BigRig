@section('title', 'Enquiries')
<x-app-layout>
    <x-portal.page title="Enquiries" subtitle="Messages sent through the website contact form. Open one to read it and reply by email, phone or WhatsApp.">

        {{-- Tabs --}}
        <nav class="mb-6 flex gap-1 overflow-x-auto rounded-xl bg-white p-1 shadow-sm ring-1 ring-ink-100" aria-label="Enquiries">
            @foreach (['unread' => 'Unread', 'all' => 'All'] as $key => $label)
                <a href="{{ route('admin.enquiries.index', array_filter(['tab' => $key, 'q' => $search])) }}"
                   @class(['flex shrink-0 items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition', 'bg-ink-950 text-white' => $tab === $key, 'text-ink-600 hover:bg-ink-50' => $tab !== $key])
                   @if ($tab === $key) aria-current="page" @endif>
                    {{ $label }}
                    <span @class(['rounded-full px-2 py-0.5 text-xs', 'bg-brand-400 text-ink-950' => $tab === $key, 'bg-ink-100 text-ink-600' => $tab !== $key])>{{ $counts[$key] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="portal-card">
            <form method="GET" class="flex flex-col gap-3 border-b border-ink-100 p-4 sm:flex-row">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <label class="relative flex-1">
                    <span class="sr-only">Search</span>
                    <x-site.icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-400" />
                    <input type="search" name="q" value="{{ $search }}" placeholder="Name, email, phone or words in the message" class="portal-input pl-9">
                </label>
                <div class="flex gap-2">
                    <button class="btn-dark btn-sm flex-1"><x-site.icon name="search" class="h-4 w-4" /> Search</button>
                    @if ($search)
                        <a href="{{ route('admin.enquiries.index', ['tab' => $tab]) }}" class="btn-ghost btn-sm">Clear</a>
                    @endif
                </div>
            </form>

            @if ($enquiries->isEmpty())
                @if ($search)
                    <x-portal.empty icon="search" title="No matches" text="No enquiries match “{{ $search }}”." />
                @elseif ($tab === 'unread')
                    <x-portal.empty icon="check-circle" title="You're all caught up" text="There are no unread enquiries. New messages from the website contact form will appear here." />
                @else
                    <x-portal.empty icon="inbox" title="No enquiries yet" text="Messages sent through the website contact form will appear here." />
                @endif
            @else
                <ul class="divide-y divide-ink-100">
                    @foreach ($enquiries as $enquiry)
                        @php $unread = ! $enquiry->read_at; @endphp
                        <li>
                            <a href="{{ route('admin.enquiries.show', $enquiry) }}" @class(['flex gap-3 px-4 py-4 transition hover:bg-ink-50 sm:px-5', 'bg-brand-50/40' => $unread])>
                                <span class="mt-2 flex w-2.5 shrink-0 justify-center">
                                    @if ($unread)<span class="h-2.5 w-2.5 rounded-full bg-brand-500" title="Unread"></span><span class="sr-only">Unread</span>@endif
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1">
                                        <span @class(['truncate text-ink-950', 'font-bold' => $unread, 'font-medium' => ! $unread])>{{ $enquiry->name }}</span>
                                        <span class="shrink-0 text-xs text-ink-400" title="{{ $enquiry->created_at }}">{{ $enquiry->created_at?->diffForHumans() }}</span>
                                    </span>
                                    <span @class(['mt-0.5 block truncate text-sm', 'font-semibold text-ink-900' => $unread, 'text-ink-700' => ! $unread])>
                                        {{ $enquiry->subject ?: 'Website enquiry' }}
                                        @if ($enquiry->course)<span class="badge-grey ml-1 align-middle">{{ $enquiry->course->objective }}</span>@endif
                                    </span>
                                    <span class="mt-0.5 block truncate text-sm text-ink-500">{{ \Illuminate\Support\Str::limit($enquiry->message, 160) }}</span>
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
                @if ($enquiries->hasPages())
                    <div class="border-t border-ink-100 px-4 py-3">{{ $enquiries->links() }}</div>
                @endif
            @endif
        </div>
    </x-portal.page>
</x-app-layout>
