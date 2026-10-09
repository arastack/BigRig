<x-site.auth title="Verify your email">
    <h1 class="text-5xl">Check your inbox</h1>
    <p class="mt-2 text-ink-500">We sent a verification link to <strong class="text-ink-900">{{ auth()->user()?->email }}</strong>. Click it to confirm your email address.</p>
    <div class="mt-8 space-y-4">
        @if (session('status') == 'verification-link-sent')
            <div class="flex gap-3 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-900" role="status"><x-site.icon name="check-circle" class="h-5 w-5 shrink-0" />A new verification link has been sent.</div>
        @endif
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button class="btn-brand w-full">Resend verification email</button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn-outline w-full">Sign out</button>
        </form>
    </div>
</x-site.auth>
