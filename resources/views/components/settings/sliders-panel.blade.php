<div class="grow">

    <!-- Panel body -->
    <div class="p-6 space-y-6">
        <section>
            <div class="mb-8">
                <h2 class="text-2xl text-gray-800 dark:text-gray-100 font-bold mb-4">Sliders</h2>
            </div>

            @if ($sliders->isEmpty())
                <div class="col-span-full bg-white dark:bg-gray-800 shadow-sm rounded-xl">
                    <div class="flex flex-col h-full text-center p-5">
                        <div class="grow mb-1">
                            <h3 class="text-lg text-gray-800 dark:text-gray-100 font-semibold mb-1">No record found.</h3>
                        </div>
                    </div>
                </div>
            @else
                <ul>
                    @foreach ($sliders as $slider)
                        <li class="sm:flex items-center py-6 border-b border-gray-200 dark:border-gray-700/60">
                            <a class="block mb-4 sm:mb-0 mr-5 md:w-32 xl:w-auto shrink-0" href="javascript:void(0)">
                                <img class="w-full rounded-sm h-40" src="{{ Storage::url($slider->image_url) }}" width="200" height="142" alt="{{ $slider->title }}" />
                            </a>
                            <div class="grow">
                                <a href="javascript:void(0)">
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-1">{{$slider->title }}</h3>
                                </a>
                                <div class="text-sm mb-4">{!! clean_html($slider->text) !!}</div>
                                <div class="text-sm text-yellow-500 mb-2">Button: 
                                    <a href="{{ $slider->button_url ? url($slider->button_url) : 'javascript:void(0)' }}" 
                                    title="{{ $slider->button_url ?? 'No URL Provided' }}">
                                        {{ $slider->button_name }}
                                    </a>
                                </div>
                                <!-- Product meta -->
                                <div class="flex flex-wrap justify-between items-center">
                                    <!-- Rating and price -->
                                    <div class="flex flex-wrap items-center space-x-2 mr-2">
                                    </div>
                                    <button class="text-sm text-violet-500 underline hover:no-underline" @click="$store.editModal.open({ id: {{ $slider->id }}, slider_title: '{{ $slider->title }}', slider_text: @js(clean_html($slider->text)), button_name: '{{ $slider->button_name }}', button_url: '{{ $slider->button_url }}'})" aria-controls="edit-modal">Edit</button>
                                    <button class="text-sm text-red-500 underline hover:no-underline" @click="$store.deleteModal.open({{ $slider->id }})" aria-controls="delete-modal">Remove</button>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

    </div>
</div>

<!-- Edit Slider Modal -->
@include('components.modals.edit-slider-modal')

<!-- Delete Slider Modal -->
@include('components.modals.delete-slider-modal')