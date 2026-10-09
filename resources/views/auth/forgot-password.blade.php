<x-site.auth title="Reset your password">
    <h1 class="text-5xl">Forgot your password?</h1>
    <p class="mt-2 text-ink-500">Enter the email address you applied with and we'll send you a link to choose a new password.</p>
    <div class="mt-8">
        <x-site.status />
        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf
            <x-site.input name="email" label="Email address" type="email" required autofocus autocomplete="username" />
            <button class="btn-brand w-full">Email me a reset link</button>
        </form>
        <p class="mt-6 text-sm text-ink-500"><a href="{{ route('login') }}" class="link-underline">Back to sign in</a></p>
    </div>
</x-site.auth>
