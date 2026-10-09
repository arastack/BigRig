<x-site.auth title="Confirm password">
    <h1 class="text-5xl">Confirm it's you</h1>
    <p class="mt-2 text-ink-500">This is a secure area. Please enter your password to continue.</p>
    <div class="mt-8">
        <x-site.status />
        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
            @csrf
            <x-site.input name="password" label="Password" type="password" required autofocus autocomplete="current-password" />
            <button class="btn-brand w-full">Confirm</button>
        </form>
    </div>
</x-site.auth>
