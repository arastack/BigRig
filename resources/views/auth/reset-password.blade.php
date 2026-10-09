<x-site.auth title="Choose a new password">
    <h1 class="text-5xl">Choose a new password</h1>
    <p class="mt-2 text-ink-500">Use at least 8 characters. A mix of words, numbers and symbols is strongest.</p>
    <div class="mt-8">
        <x-site.status />
        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <x-site.input name="email" label="Email address" type="email" :value="$request->email" required autocomplete="username" />
            <x-site.input name="password" label="New password" type="password" required autocomplete="new-password" />
            <x-site.input name="password_confirmation" label="Confirm new password" type="password" required autocomplete="new-password" />
            <button class="btn-brand w-full">Save new password</button>
        </form>
    </div>
</x-site.auth>
