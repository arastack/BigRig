<x-site.layout :title="$title">
    <section class="on-dark relative overflow-hidden bg-ink-950 text-white">
        <div class="container-site flex min-h-[60vh] flex-col items-start justify-center py-24">
            <p class="font-display text-[8rem] font-extrabold leading-none text-brand-400 sm:text-[12rem]">{{ $code }}</p>
            <h1 class="mt-2 text-5xl text-white sm:text-6xl">{{ $title }}</h1>
            <p class="mt-4 max-w-xl text-lg text-ink-300">{{ $message }}</p>
            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="{{ url('/') }}" class="btn-brand">Back to home</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-outline-light">My portal</a>
                @else
                    <a href="{{ route('contact') }}" class="btn-outline-light">Contact us</a>
                @endauth
            </div>
        </div>
        <div class="lane absolute inset-x-0 bottom-8 opacity-70" aria-hidden="true"></div>
    </section>
</x-site.layout>
