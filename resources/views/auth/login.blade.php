<x-site.auth :title="page_text('login.meta.title')">
    <h1 class="text-5xl">{{ page_text('login.title') }}</h1>
    <p class="mt-2 text-ink-500">{{ page_text('login.lead') }}</p>

    <div class="mt-8">
        <x-site.status />

        <form method="POST" action="{{ route('login') }}" class="space-y-5" x-data="{ sending: false, show: false }" @submit="sending = true">
            @csrf
            <x-site.input name="email" :label="page_text('login.email')" type="email" required autofocus autocomplete="username" />

            <div>
                <div class="flex items-center justify-between">
                    <label for="password" class="form-label">{{ page_text('login.password') }}</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="mb-1.5 text-sm font-medium text-ink-500 hover:text-ink-950">{{ page_text('login.forgot') }}</a>
                    @endif
                </div>
                <div class="relative">
                    <input id="password" name="password" :type="show ? 'text' : 'password'" type="password" required autocomplete="current-password" class="form-field pr-20">
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-4 text-sm font-semibold text-ink-500 hover:text-ink-950" x-text="show ? {{ \Illuminate\Support\Js::from(page_text('login.hide')) }} : {{ \Illuminate\Support\Js::from(page_text('login.show')) }}" aria-controls="password">{{ page_text('login.show') }}</button>
                </div>
            </div>

            <label class="flex items-center gap-3 text-sm text-ink-600">
                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-ink-300 text-ink-950 focus:ring-brand-400"> {{ page_text('login.remember') }}
            </label>

            <button class="btn-brand w-full" :disabled="sending">
                <span x-show="!sending">{{ page_text('login.button') }}</span><span x-show="sending" x-cloak>{{ page_text('login.sending') }}</span>
            </button>
        </form>

        <div class="mt-8 rounded-lg bg-ink-50 p-5 text-sm">
            <p class="font-semibold text-ink-900">{{ page_text('login.new.title') }}</p>
            <p class="mt-1 text-ink-500">{!! page_html('login.new.text', ['{pdf_url}' => route('register.form-pdf')], 'link-underline') !!}</p>
            <a href="{{ route('register') }}" class="btn-dark btn-sm mt-4">{{ page_text('login.new.button') }}</a>
        </div>
    </div>
</x-site.auth>
