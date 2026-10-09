<x-site.auth title="Two-factor authentication">
    <div x-data="{ recovery: false }">
        <h1 class="text-5xl">Two-step verification</h1>
        <p class="mt-2 text-ink-500" x-show="! recovery">Enter the 6-digit code from your authenticator app.</p>
        <p class="mt-2 text-ink-500" x-show="recovery" x-cloak>Enter one of your emergency recovery codes.</p>
        <div class="mt-8">
            <x-site.status />
            <form method="POST" action="{{ route('two-factor.login') }}" class="space-y-5">
                @csrf
                <div x-show="! recovery">
                    <x-site.input name="code" label="Authentication code" inputmode="numeric" autocomplete="one-time-code" x-ref="code" autofocus />
                </div>
                <div x-show="recovery" x-cloak>
                    <x-site.input name="recovery_code" label="Recovery code" autocomplete="one-time-code" x-ref="recovery_code" />
                </div>
                <button class="btn-brand w-full">Continue</button>
                <button type="button" class="w-full text-sm font-semibold text-ink-500 hover:text-ink-950"
                        @click="recovery = ! recovery; $nextTick(() => recovery ? $refs.recovery_code.focus() : $refs.code.focus())"
                        x-text="recovery ? 'Use an authentication code' : 'Use a recovery code'">Use a recovery code</button>
            </form>
        </div>
    </div>
</x-site.auth>
