<x-site.auth title="Sign in">
    <h1 class="text-5xl">Welcome back</h1>
    <p class="mt-2 text-ink-500">Sign in to track your application, pay fees or open your student or staff portal.</p>

    <div class="mt-8">
        <x-site.status />

        <form method="POST" action="{{ route('login') }}" class="space-y-5" x-data="{ sending: false, show: false }" @submit="sending = true">
            @csrf
            <x-site.input name="email" label="Email address" type="email" required autofocus autocomplete="username" />

            <div>
                <div class="flex items-center justify-between">
                    <label for="password" class="form-label">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="mb-1.5 text-sm font-medium text-ink-500 hover:text-ink-950">Forgot password?</a>
                    @endif
                </div>
                <div class="relative">
                    <input id="password" name="password" :type="show ? 'text' : 'password'" type="password" required autocomplete="current-password" class="form-field pr-20">
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-4 text-sm font-semibold text-ink-500 hover:text-ink-950" x-text="show ? 'Hide' : 'Show'" aria-controls="password">Show</button>
                </div>
            </div>

            <label class="flex items-center gap-3 text-sm text-ink-600">
                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-ink-300 text-ink-950 focus:ring-brand-400"> Keep me signed in
            </label>

            <button class="btn-brand w-full" :disabled="sending">
                <span x-show="!sending">Sign in</span><span x-show="sending" x-cloak>Signing in…</span>
            </button>
        </form>

        <div class="mt-8 rounded-lg bg-ink-50 p-5 text-sm">
            <p class="font-semibold text-ink-900">New to BigRig?</p>
            <p class="mt-1 text-ink-500">Apply online in about 10 minutes, or <a href="{{ route('register.form-pdf') }}" class="link-underline">download the paper form</a>.</p>
            <a href="{{ route('register') }}" class="btn-dark btn-sm mt-4">Start an application</a>
        </div>
    </div>
</x-site.auth>
