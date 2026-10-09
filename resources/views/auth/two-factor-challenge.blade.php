<x-site.auth :title="page_text('two_factor.meta.title')">
    <div x-data="{ recovery: false }">
        <h1 class="text-5xl">{{ page_text('two_factor.title') }}</h1>
        <p class="mt-2 text-ink-500" x-show="! recovery">{{ page_text('two_factor.lead') }}</p>
        <p class="mt-2 text-ink-500" x-show="recovery" x-cloak>{{ page_text('two_factor.lead_recovery') }}</p>
        <div class="mt-8">
            <x-site.status />
            <form method="POST" action="{{ route('two-factor.login') }}" class="space-y-5">
                @csrf
                <div x-show="! recovery">
                    <x-site.input name="code" :label="page_text('two_factor.code')" inputmode="numeric" autocomplete="one-time-code" x-ref="code" autofocus />
                </div>
                <div x-show="recovery" x-cloak>
                    <x-site.input name="recovery_code" :label="page_text('two_factor.recovery')" autocomplete="one-time-code" x-ref="recovery_code" />
                </div>
                <button class="btn-brand w-full">{{ page_text('two_factor.button') }}</button>
                <button type="button" class="w-full text-sm font-semibold text-ink-500 hover:text-ink-950"
                        @click="recovery = ! recovery; $nextTick(() => recovery ? $refs.recovery_code.focus() : $refs.code.focus())"
                        x-text="recovery ? {{ \Illuminate\Support\Js::from(page_text('two_factor.use_code')) }} : {{ \Illuminate\Support\Js::from(page_text('two_factor.use_recovery')) }}">{{ page_text('two_factor.use_recovery') }}</button>
            </form>
        </div>
    </div>
</x-site.auth>
