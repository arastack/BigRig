<x-site.auth :title="page_text('verify.meta.title')">
    <h1 class="text-5xl">{{ page_text('verify.title') }}</h1>
    <p class="mt-2 text-ink-500">{!! strtr(e(page_text('verify.lead')), [':email' => '<strong class="text-ink-900">' . e(auth()->user()?->email) . '</strong>']) !!}</p>
    <div class="mt-8 space-y-4">
        @if (session('status') == 'verification-link-sent')
            <div class="flex gap-3 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-900" role="status"><x-site.icon name="check-circle" class="h-5 w-5 shrink-0" />{{ page_text('verify.sent') }}</div>
        @endif
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button class="btn-brand w-full">{{ page_text('verify.resend') }}</button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn-outline w-full">{{ page_text('verify.sign_out') }}</button>
        </form>
    </div>
</x-site.auth>
