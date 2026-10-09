@props(['name', 'title', 'maxWidth' => 'max-w-lg'])
{{-- Open with $dispatch('open-modal', '{{ $name }}') --}}
<div x-data="{ open: false }" x-on:open-modal.window="if ($event.detail === @js($name)) open = true" x-on:close-modal.window="open = false" @keydown.escape.window="open = false">
    <div x-show="open" x-cloak class="fixed inset-0 z-[70] flex items-end justify-center p-4 sm:items-center" role="dialog" aria-modal="true" aria-labelledby="modal-{{ $name }}-title">
        <div x-show="open" x-transition.opacity class="absolute inset-0 bg-ink-950/60" @click="open = false"></div>
        <div x-show="open" x-transition x-trap.noscroll="open" class="relative w-full {{ $maxWidth }} max-h-[90vh] overflow-y-auto rounded-xl bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-ink-100 px-6 py-4">
                <h2 id="modal-{{ $name }}-title" class="font-display text-2xl font-bold uppercase text-ink-950">{{ $title }}</h2>
                <button type="button" class="rounded-md p-1 text-ink-400 hover:bg-ink-50 hover:text-ink-950" @click="open = false" aria-label="Close"><x-site.icon name="x" class="h-5 w-5" /></button>
            </div>
            <div class="p-6">{{ $slot }}</div>
        </div>
    </div>
</div>
