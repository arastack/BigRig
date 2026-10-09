@section('title', 'Enquiry from ' . $enquiry->name)
@php
    $subject = $enquiry->subject ?: 'Your enquiry to ' . ($settings->site_name ?? 'BigRig International Truck Driving School');
    $mailto = 'mailto:' . $enquiry->email . '?subject=' . rawurlencode('Re: ' . $subject);
    $digits = preg_replace('/\D+/', '', (string) $enquiry->phone);
    $whatsapp = $digits ? 'https://wa.me/' . (str_starts_with($digits, '0') ? '234' . substr($digits, 1) : $digits) : null;
@endphp
<x-app-layout>
    <x-portal.page :title="$enquiry->name" :subtitle="'Sent ' . $enquiry->created_at?->format('l j F Y, g:ia') . ' · ' . $enquiry->created_at?->diffForHumans()" :back="route('admin.enquiries.index')">
        <x-slot:actions>
            <form method="POST" action="{{ route('admin.enquiries.update', $enquiry) }}">
                @csrf @method('PATCH')
                <button class="btn-ghost btn-sm"><x-site.icon name="mail" class="h-4 w-4" /> Mark as unread</button>
            </form>
            <form method="POST" action="{{ route('admin.enquiries.destroy', $enquiry) }}" onsubmit="return confirm('Delete this enquiry? This cannot be undone.')">
                @csrf @method('DELETE')
                <button class="btn-danger btn-sm"><x-site.icon name="trash" class="h-4 w-4" /> Delete</button>
            </form>
        </x-slot:actions>

        <div class="grid gap-6 xl:grid-cols-3">
            {{-- Message --}}
            <section class="portal-card xl:col-span-2">
                <div class="portal-card-header">
                    <h2 class="portal-card-title">{{ $enquiry->subject ?: 'Website enquiry' }}</h2>
                </div>
                <div class="p-5 sm:p-6">
                    <div class="whitespace-pre-line break-words leading-relaxed text-ink-900">{{ $enquiry->message }}</div>
                </div>
                <div class="flex flex-wrap gap-2 border-t border-ink-100 p-5 sm:px-6">
                    <a href="{{ $mailto }}" class="btn-brand btn-sm"><x-site.icon name="send" class="h-4 w-4" /> Reply by email</a>
                    @if ($digits)
                        <a href="tel:{{ preg_replace('/[^\d+]/', '', $enquiry->phone) }}" class="btn-ghost btn-sm"><x-site.icon name="phone" class="h-4 w-4" /> Call</a>
                        <a href="{{ $whatsapp }}" target="_blank" rel="noopener" class="btn-ghost btn-sm"><x-site.icon name="whatsapp" class="h-4 w-4" /> WhatsApp</a>
                    @endif
                </div>
            </section>

            {{-- Contact details --}}
            <aside class="space-y-6">
                <section class="portal-card">
                    <div class="portal-card-header">
                        <h2 class="portal-card-title">Contact details</h2>
                    </div>
                    <dl class="space-y-4 p-5 text-sm sm:p-6">
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-ink-400">Name</dt>
                            <dd class="mt-0.5 text-ink-900">{{ $enquiry->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-ink-400">Email</dt>
                            <dd class="mt-0.5 break-all"><a href="{{ $mailto }}" class="text-ink-900 underline decoration-brand-400 decoration-2 underline-offset-4">{{ $enquiry->email }}</a></dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-ink-400">Phone</dt>
                            <dd class="mt-0.5 text-ink-900">
                                @if ($digits)
                                    <a href="tel:{{ preg_replace('/[^\d+]/', '', $enquiry->phone) }}" class="underline decoration-brand-400 decoration-2 underline-offset-4">{{ $enquiry->phone }}</a>
                                @else
                                    Not given
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-ink-400">Course of interest</dt>
                            <dd class="mt-0.5 text-ink-900">{{ $enquiry->course?->objective ?? 'Not specified' }}</dd>
                        </div>
                    </dl>
                </section>

                <section class="portal-card">
                    <dl class="space-y-4 p-5 text-sm sm:p-6">
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-ink-400">Read by</dt>
                            <dd class="mt-0.5 text-ink-900">
                                {{ $enquiry->handler ? trim($enquiry->handler->firstName . ' ' . $enquiry->handler->lastName) : '—' }}
                                @if ($enquiry->read_at)<span class="text-ink-400"> · {{ $enquiry->read_at->diffForHumans() }}</span>@endif
                            </dd>
                        </div>
                        @if ($enquiry->ip_address)
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-ink-400">Sent from IP</dt>
                                <dd class="mt-0.5 font-mono text-xs text-ink-600">{{ $enquiry->ip_address }}</dd>
                            </div>
                        @endif
                    </dl>
                </section>
            </aside>
        </div>
    </x-portal.page>
</x-app-layout>
