<x-site.auth :title="page_text('forgot.meta.title')">
    <h1 class="text-5xl">{{ page_text('forgot.title') }}</h1>
    <p class="mt-2 text-ink-500">{{ page_text('forgot.lead') }}</p>
    <div class="mt-8">
        <x-site.status />
        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf
            <x-site.input name="email" :label="page_text('forgot.email')" type="email" required autofocus autocomplete="username" />
            <button class="btn-brand w-full">{{ page_text('forgot.button') }}</button>
        </form>
        <p class="mt-6 text-sm text-ink-500"><a href="{{ route('login') }}" class="link-underline">{{ page_text('forgot.back') }}</a></p>
    </div>
</x-site.auth>
