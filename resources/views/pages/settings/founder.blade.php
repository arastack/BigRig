@section('title', 'Founder message')

<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

    <!-- Page header -->
    <div class="mb-8">

        <!-- Title -->
        <h1 class="font-display text-4xl font-bold uppercase leading-none tracking-tight text-ink-950 sm:text-5xl">Founder message</h1>
            <p class="mt-2 text-ink-500">Content shown on the public website.</p>

    </div>

    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl mb-8">
        <div class="flex flex-col md:flex-row md:-mr-px">

            <!-- Sidebar -->
            <x-settings.settings-sidebar />

    
            <x-settings.founder-panel :founder="$founder" />

            <!-- Sidebar -->
            <div>
                <div class="lg:sticky lg:top-16 bg-gradient-to-r from-white/30 dark:from-gray-800/30 lg:overflow-x-hidden lg:overflow-y-auto no-scrollbar lg:shrink-0 border-t lg:border-t-0 lg:border-l border-gray-200 dark:border-gray-700/60 lg:w-[320px] xl:w-[352px] 2xl:w-[calc(352px+80px)] lg:h-[calc(100dvh-64px)]">
                    <div class="py-8 px-4 lg:px-8 2xl:px-12">
                        <div class="max-w-sm mx-auto lg:max-w-none">
                            <div class="mb-5">
                                <h2 class="text-2xl text-gray-800 dark:text-gray-100 font-bold mb-6">Founder's Picture</h2>
                                    <div>
                                        <div class="space-y-4">
                                            <img class="rounded-sm h-56" src="{{ optional($founder)->founder_picture ? Storage::url($founder->founder_picture) : '' }}" width="200" height="142" alt="Product 01" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-5">
                                <h2 class="text-2xl text-gray-800 dark:text-gray-100 font-bold mb-6">Complementing Picture</h2>
                                    <div>
                                        <div class="space-y-4">
                                            <img class="rounded-sm h-56" src="{{ optional($founder)->secondary_picture ? Storage::url($founder->secondary_picture) : '' }}" width="200" height="142" alt="Product 01" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
