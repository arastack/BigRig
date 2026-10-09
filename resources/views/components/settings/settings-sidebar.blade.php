@php
    // Secondary navigation for website content and system settings, filtered by permission
    $groups = [
        'Website content' => [
            ['Homepage sliders', 'sliders', 'globe', 'update_website_management'],
            ['About the school', 'about-company', 'building', 'update_website_management'],
            ['Founder message', 'founder', 'quote', 'update_website_management'],
            ['Services', 'custom-services', 'wrench', 'update_website_management'],
            ['FAQs', 'faqs', 'book', 'update_website_management'],
            ['Partners & clients', 'clients', 'users', 'update_website_management'],
            ['Achievements', 'achievements', 'award', 'update_website_management'],
            ['Courses & fees', 'objectives', 'truck', 'manage_course_catalogue'],
            ['Packages & VAT', 'packages', 'award', 'manage_course_catalogue'],
            ['Page text', 'admin.page-text', 'pencil', 'update_website_management'],
        ],
        'System' => [
            ['Site information', 'site_settings', 'gauge', 'update_site_settings'],
            ['Email server', 'email-config', 'send', 'update_email_configuration'],
            ['Payment gateways', 'payment-gateway-config', 'key', 'update_payment_configuration'],
        ],
    ];
@endphp
<nav class="no-scrollbar flex min-w-60 flex-nowrap gap-6 overflow-x-auto border-b border-ink-100 px-3 py-5 md:block md:space-y-6 md:overflow-auto md:border-b-0 md:border-r" aria-label="Settings">
    @foreach ($groups as $title => $items)
        @php $items = array_filter($items, fn ($item) => auth()->user()->hasPermission($item[3])); @endphp
        @if ($items)
            <div>
                <p class="mb-2 px-2.5 font-display text-xs font-semibold uppercase tracking-[0.2em] text-ink-400">{{ $title }}</p>
                <ul class="flex flex-nowrap gap-1 md:block md:space-y-0.5">
                    @foreach ($items as [$label, $route, $icon])
                        @php $active = request()->routeIs($route); @endphp
                        <li>
                            <a href="{{ route($route) }}" @class(['flex items-center gap-2.5 whitespace-nowrap rounded-lg px-2.5 py-2 text-sm font-medium transition', 'bg-ink-950 text-white' => $active, 'text-ink-600 hover:bg-ink-50 hover:text-ink-950' => ! $active]) @if ($active) aria-current="page" @endif>
                                <x-site.icon :name="$icon" @class(['h-4 w-4 shrink-0', 'text-brand-400' => $active, 'text-ink-400' => ! $active]) />
                                {{ $label }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endforeach
</nav>
