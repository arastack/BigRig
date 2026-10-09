<x-site.auth :title="page_text('reset.meta.title')">
    <h1 class="text-5xl">{{ page_text('reset.title') }}</h1>
    <p class="mt-2 text-ink-500">{{ page_text('reset.lead') }}</p>
    <div class="mt-8">
        <x-site.status />
        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <x-site.input name="email" :label="page_text('reset.email')" type="email" :value="$request->email" required autocomplete="username" />
            <x-site.input name="password" :label="page_text('reset.password')" type="password" required autocomplete="new-password" />
            <x-site.input name="password_confirmation" :label="page_text('reset.confirm')" type="password" required autocomplete="new-password" />
            <button class="btn-brand w-full">{{ page_text('reset.button') }}</button>
        </form>
    </div>
</x-site.auth>
