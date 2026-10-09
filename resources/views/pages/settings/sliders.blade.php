@section('title', 'Homepage sliders')

<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

    <!-- Page header -->
    <div class="mb-8">

        <!-- Title -->
        <h1 class="font-display text-4xl font-bold uppercase leading-none tracking-tight text-ink-950 sm:text-5xl">Homepage sliders</h1>
            <p class="mt-2 text-ink-500">The rotating hero messages and vehicle images on the homepage.</p>

    </div>

    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl mb-8">
        <div class="flex flex-col md:flex-row md:-mr-px">

            <!-- Sidebar -->
            <x-settings.settings-sidebar />

    
            <x-settings.sliders-panel :sliders="$sliders" />

            <!-- Sidebar -->
            <form method="POST" action="{{ route('slider.store') }}" enctype="multipart/form-data">
            @csrf
                <div>
                    <div class="lg:sticky lg:top-16 bg-gradient-to-r from-white/30 dark:from-gray-800/30 lg:overflow-x-hidden lg:overflow-y-auto no-scrollbar lg:shrink-0 border-t lg:border-t-0 lg:border-l border-gray-200 dark:border-gray-700/60 lg:w-[320px] xl:w-[352px] 2xl:w-[calc(352px+80px)] lg:h-[calc(100dvh-64px)]">
                        <div class="py-8 px-4 lg:px-8 2xl:px-12">
                            <div class="max-w-sm mx-auto lg:max-w-none">
                                <h2 class="text-2xl text-gray-800 dark:text-gray-100 font-bold mb-6">Add New Slider</h2>

                                    <!-- Payment Details -->
                                    <div>
                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium mb-1" for="card-nr">Landing Page 1 - Slider Picture <span class="text-red-500">*</span> <span class="text-yellow-500 text-xs">1029 x 631 px</span></label>
                                                <input id="card-nr" class="form-input w-full" type="file" name="image_url" required />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium mb-1" for="card-nr">Landing Page 2 - Slider Picture <span class="text-red-500">*</span> <span class="text-yellow-500 text-xs">785 x 670 px</span></label>
                                                <input id="card-nr" class="form-input w-full" type="file" name="image_url_2" required />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium mb-1" for="card-name">Slider Title <span class="text-red-500">*</span></label>
                                                <input id="card-name" class="form-input w-full" type="text" placeholder="Need Car For Driving Test" name="slider_title" required />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium mb-1" for="slider_text">Slider Text <span class="text-red-500">*</span></label>
                                                <textarea id="slider_text" class="form-textarea w-full px-2 py-1" rows="4" placeholder="Register with us and embark on a journey towards a brighter future behind the wheel." name="slider_text" required></textarea>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium mb-1" for="card-name">Button Name </label>
                                                <input id="card-name" class="form-input w-full" type="text" placeholder="Register Now" name="button_name" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium mb-1" for="card-name">Redirect To </label>
                                                <input id="card-name" class="form-input w-full" type="text" placeholder="/register" name="button_url" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-6 flex justify-end">
                                        <div class="mb-4">
                                            <button type="submit" class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{$sliders->links()}}
        </div>

    </div>
</x-app-layout>

@include('pages.app-layout-scripts.sliders')