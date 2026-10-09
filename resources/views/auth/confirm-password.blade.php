<x-site.auth :title="page_text('confirm.meta.title')">
    <h1 class="text-5xl">{{ page_text('confirm.title') }}</h1>
    <p class="mt-2 text-ink-500">{{ page_text('confirm.lead') }}</p>
    <div class="mt-8">
        <x-site.status />
        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
            @csrf
            <x-site.input name="password" :label="page_text('confirm.password')" type="password" required autofocus autocomplete="current-password" />
            <button class="btn-brand w-full">{{ page_text('confirm.button') }}</button>
        </form>
    </div>
</x-site.auth>
