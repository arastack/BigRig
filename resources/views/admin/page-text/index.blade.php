@section('title', 'Page text')
@php
    $active = request('tab');
    $active = $groups->contains('id', $active) ? $active : ($groups->first()['id'] ?? null);
@endphp
<x-app-layout>
    <x-portal.page title="Page text" :back="route('admin.content')"
        subtitle="Headings, introductions, buttons and legal pages on the public website. Change the wording, save, and the site updates straight away.">
        <x-slot:actions>
            <a href="{{ route('index') }}" target="_blank" rel="noopener" class="btn-ghost btn-sm"><x-site.icon name="external" class="h-4 w-4" /> Open website</a>
        </x-slot:actions>

        <form method="POST" action="{{ route('admin.page-text.update') }}" x-data="{ tab: @js($active) }"
              @submit="$el.querySelectorAll('[role=tabpanel]').forEach(panel => { if (panel.id !== 'panel-' + tab) panel.querySelectorAll('input, textarea, select').forEach(field => field.disabled = true) })">
            {{-- Only the open tab is submitted, keeping each save far below PHP's max_input_vars limit --}}
            @csrf
            @method('PUT')
            <input type="hidden" name="tab" :value="tab" value="{{ $active }}">

            {{-- Page tabs --}}
            <nav class="mb-6 flex flex-wrap gap-1 rounded-xl bg-white p-1 shadow-sm ring-1 ring-ink-100" aria-label="Pages" role="tablist">
                @foreach ($groups as $group)
                    <button type="button" role="tab" id="tab-{{ $group['id'] }}" aria-controls="panel-{{ $group['id'] }}"
                            @click="tab = @js($group['id']); history.replaceState(null, '', '?tab=' + tab)"
                            :aria-selected="(tab === @js($group['id'])).toString()"
                            :class="tab === @js($group['id']) ? 'bg-ink-950 text-white' : 'text-ink-600 hover:bg-ink-50'"
                            class="flex shrink-0 items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition">
                        {{ $group['label'] }}
                        @if ($group['customised'])
                            <span class="rounded-full bg-brand-400 px-2 py-0.5 text-xs text-ink-950" title="{{ $group['customised'] }} changed from the default">{{ $group['customised'] }}</span>
                        @endif
                    </button>
                @endforeach
            </nav>

            @foreach ($groups as $group)
                <section id="panel-{{ $group['id'] }}" role="tabpanel" aria-labelledby="tab-{{ $group['id'] }}"
                         x-show="tab === @js($group['id'])" @if ($active !== $group['id']) x-cloak style="display: none" @endif
                         class="portal-card">
                    <div class="portal-card-header">
                        <div class="min-w-0">
                            <h2 class="portal-card-title">{{ $group['label'] }}</h2>
                            @if ($group['help'])<p class="mt-0.5 text-sm text-ink-500">{{ $group['help'] }}</p>@endif
                        </div>
                        @if ($group['url'])
                            <a href="{{ $group['url'] }}" target="_blank" rel="noopener" class="btn-ghost btn-sm"><x-site.icon name="external" class="h-4 w-4" /> View page</a>
                        @endif
                    </div>

                    <div class="divide-y divide-ink-100">
                        @foreach ($group['fields'] as $field)
                            @php $id = 'text-' . str_replace('.', '-', $field['key']); @endphp
                            <div class="grid gap-2 px-5 py-4 lg:grid-cols-[16rem_1fr] lg:gap-6" x-data="{ cleared: false }">
                                <div class="flex items-start justify-between gap-2 lg:flex-col lg:justify-start">
                                    <label for="{{ $id }}" class="portal-label">{{ $field['label'] }}</label>
                                    <div class="flex flex-wrap items-center gap-2">
                                        @if ($field['customised'])
                                            <span class="badge-yellow" x-show="!cleared">Edited</span>
                                        @endif
                                        <span class="badge-grey" x-show="cleared" x-cloak>Default on save</span>
                                        <button type="button" class="inline-flex items-center gap-1 text-xs font-medium text-ink-500 hover:text-ink-950"
                                                title="Clear this field so the original wording is used when you save"
                                                @click="cleared = true; @if ($field['type'] === 'rich') window.CKEDITOR?.instances[@js($id)]?.setData(''); @endif $refs.input.value = ''">
                                            <x-site.icon name="refresh" class="h-3.5 w-3.5" /> Reset to default
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    @if ($field['type'] === 'text')
                                        <input id="{{ $id }}" x-ref="input" type="text" name="text[{{ $field['key'] }}]" value="{{ $field['value'] }}" placeholder="{{ $field['default'] }}" class="portal-input" @input="cleared = false">
                                    @elseif ($field['type'] === 'textarea')
                                        <textarea id="{{ $id }}" x-ref="input" name="text[{{ $field['key'] }}]" rows="{{ max(2, min(6, (int) ceil(mb_strlen($field['value']) / 90))) }}" placeholder="{{ $field['default'] }}" class="portal-input" @input="cleared = false">{{ $field['value'] }}</textarea>
                                    @else
                                        <textarea id="{{ $id }}" x-ref="input" name="text[{{ $field['key'] }}]" rows="16" class="portal-input rich-editor">{{ $field['value'] }}</textarea>
                                    @endif
                                    @if (! empty($field['help']))<p class="mt-1 text-xs text-ink-400">{{ $field['help'] }}</p>@endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach

            {{-- Save bar --}}
            <div class="sticky bottom-0 z-10 -mx-4 mt-6 flex flex-col gap-3 border-t border-ink-100 bg-white/95 px-4 py-4 backdrop-blur sm:-mx-6 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:-mx-8 lg:px-8">
                <p class="text-sm text-ink-500">Saves the page you're viewing. A blank field goes back to the original wording.</p>
                <button class="btn-brand btn-sm shrink-0"><x-site.icon name="check" class="h-4 w-4" /> Save page text</button>
            </div>
        </form>
    </x-portal.page>
</x-app-layout>
