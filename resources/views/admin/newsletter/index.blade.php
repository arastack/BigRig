@section('title', 'Newsletter')
<x-app-layout>
    <x-portal.page title="Newsletter" subtitle="Email addresses of people who signed up for news and updates on the website.">
        <x-slot:actions>
            @can('create_newsletter')
                <button type="button" class="btn-dark btn-sm" x-data @click="$dispatch('open-modal', 'add-subscribers')"><x-site.icon name="plus" class="h-4 w-4" /> Add subscribers</button>
            @endcan
            @if ($total)
                <a href="{{ route('admin.newsletter.export', request()->query()) }}" class="btn-ghost btn-sm"><x-site.icon name="download" class="h-4 w-4" /> Export CSV</a>
            @endif
        </x-slot:actions>

        @can('create_newsletter')
            <x-portal.modal name="add-subscribers" title="Add subscribers">
                <form method="POST" action="{{ route('admin.newsletter.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="emails" class="portal-label">Email addresses</label>
                        <textarea id="emails" name="emails" rows="6" required class="portal-input" placeholder="one@example.com&#10;two@example.com"></textarea>
                        <p class="mt-1 text-xs text-ink-500">One per line, or separated by commas. Only add people who agreed to receive news from the school.</p>
                    </div>
                    <div class="flex justify-end"><button class="btn-brand btn-sm">Add</button></div>
                </form>
            </x-portal.modal>
        @endcan

        <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-portal.stat label="Subscribers" :value="number_format($total)" icon="mail" />
            <x-portal.stat label="New this month" :value="number_format($thisMonth)" icon="trending-up" :tone="$thisMonth ? 'good' : 'default'" />
        </div>

        <div class="portal-card">
            <form method="GET" class="flex flex-col gap-3 border-b border-ink-100 p-4 sm:flex-row">
                <label class="relative flex-1">
                    <span class="sr-only">Search</span>
                    <x-site.icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-400" />
                    <input type="search" name="q" value="{{ $search }}" placeholder="Search by email address" class="portal-input pl-9">
                </label>
                <div class="flex gap-2">
                    <button class="btn-dark btn-sm flex-1"><x-site.icon name="search" class="h-4 w-4" /> Search</button>
                    @if ($search)
                        <a href="{{ route('newsletter') }}" class="btn-ghost btn-sm">Clear</a>
                    @endif
                </div>
            </form>

            @if ($subscribers->isEmpty())
                <x-portal.empty icon="mail" :title="$search ? 'No matches' : 'No subscribers yet'" :text="$search ? 'No email address matches “' . $search . '”.' : 'When visitors sign up for the newsletter on the website, their email addresses will appear here.'" />
            @else
                <div class="overflow-x-auto">
                    <table class="portal-table">
                        <thead>
                            <tr>
                                <th scope="col">Email</th>
                                <th scope="col">Subscribed</th>
                                @can('delete_newsletter')<th scope="col"><span class="sr-only">Remove</span></th>@endcan
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subscribers as $subscriber)
                                <tr>
                                    <td><a href="mailto:{{ $subscriber->email }}" class="font-medium text-ink-950 hover:underline">{{ $subscriber->email }}</a></td>
                                    <td class="whitespace-nowrap text-ink-500" title="{{ $subscriber->created_at }}">{{ $subscriber->created_at?->format('j M Y') ?? '—' }}</td>
                                    @can('delete_newsletter')
                                        <td class="text-right">
                                            <form method="POST" action="{{ route('admin.newsletter.destroy', $subscriber) }}" onsubmit="return confirm(@js('Remove ' . $subscriber->email . ' from the newsletter list?'))">
                                                @csrf @method('DELETE')
                                                <button class="btn-ghost btn-xs text-red-600" aria-label="Remove {{ $subscriber->email }}"><x-site.icon name="trash" class="h-4 w-4" /></button>
                                            </form>
                                        </td>
                                    @endcan
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($subscribers->hasPages())
                    <div class="border-t border-ink-100 px-4 py-3">{{ $subscribers->links() }}</div>
                @endif
            @endif
        </div>
    </x-portal.page>
</x-app-layout>
