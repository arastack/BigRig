<div class="grow">

    <!-- Panel body -->
    <div class="p-6 space-y-6">

        <!-- Plans -->
        <section>
            <div class="mb-8">
                <h2 class="text-2xl text-gray-800 dark:text-gray-100 font-bold mb-4">Courses</h2>
                <!-- <div class="text-sm">This workspace’s Basic Plan is set to <strong class="font-medium">$34</strong> per month and will renew on <strong class="font-medium">July 9, 2024</strong>.</div> -->
            </div>

            <!-- Pricing -->
            <div x-data="{ annual: true }">
                <!-- Toggle switch -->
                <div class="flex items-center space-x-3 mb-6">
                <div class="text-sm text-gray-500 font-medium">Part Payment <span class="text-red-500">(+10%)</span></div>
                    <div class="form-switch">
                        <input type="checkbox" id="toggle" class="sr-only" x-model="annual" />
                        <label class="bg-gray-400 dark:bg-gray-700" for="toggle">
                            <span class="bg-white shadow-sm" aria-hidden="true"></span>
                            <span class="sr-only">Pay annually</span>
                        </label>
                    </div>
                    <div class="text-sm text-gray-500 font-medium">Full Payment</div>
                </div>
                <!-- Pricing tabs -->
                <div class="grid grid-cols-12 gap-6">
                    <!-- Tab 1 -->
                    @foreach ($objectives as $objective)
                        <div class="relative col-span-full xl:col-span-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 shadow-sm rounded-b-lg flex flex-col justify-between">
                            <div>
                                <!-- Top Section -->
                                <div class="absolute top-0 left-0 right-0 h-0.5 bg-green-500" aria-hidden="true"></div>
                                <div class="px-5 pt-5 pb-2 border-b border-gray-200 dark:border-gray-700/60">
                                    <header class="flex items-center mb-2">
                                        <div class="w-6 h-6 rounded-full shrink-0 bg-green-500 mr-3">
                                            <svg class="w-6 h-6 fill-current text-white" viewBox="0 0 24 24">
                                                <path d="M12 17a.833.833 0 01-.833-.833 3.333 3.333 0 00-3.334-3.334.833.833 0 110-1.666 3.333 3.333 0 003.334-3.334.833.833 0 111.666 0 3.333 3.333 0 003.334 3.334.833.833 0 110 1.666 3.333 3.333 0 00-3.334 3.334c0 .46-.373.833-.833.833z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg text-gray-800 dark:text-gray-100 font-semibold">{{ $objective->objective }}</h3>
                                    </header>
                                    <div class="w-full mb-2">
                                        <img class="w-full h-40" src="{{ Storage::url($objective->image_url) }}" width="80" height="80" alt="{{ $objective->objective }}" />
                                    </div>
                                    <!-- Price -->
                                    <div class="text-gray-800 dark:text-gray-100 font-bold mb-4">
                                        <span class="text-2xl">{{ $settings->base_currency }}</span>
                                        <span class="text-3xl" 
                                            x-text="annual ? '{{ number_format($objective->price, 2) }}' : '{{ number_format($objective->price + ($objective->price * 0.1)) }}'">
                                            {{ number_format($objective->price, 2) }}
                                        </span>
                                        <span class="text-gray-500 font-medium text-sm"></span>
                                    </div>
                                </div>
                                <div class="px-5 pt-4">
                                    <div class="text-xs text-gray-800 dark:text-gray-100 font-semibold uppercase mb-4">{{ $objective->duration }} weeks training period</div>
                                    <!-- List -->
                                    <div class="course-requirements">
                                        {!! clean_html($objective->requirement) !!}
                                    </div>
                                </div>
                            </div>
                            <!-- Bottom Buttons -->
                            <div class="px-5 pt-4 pb-5 mt-auto">
                                <div class="flex gap-2">
                                    <button 
                                        class="btn border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-800 dark:text-gray-300 w-1/2" 
                                        @click="$store.editModal.open({
                                            id: {{ $objective->id }},
                                            objective: `{{ $objective->objective }}`,
                                            price: {{ $objective->price }},
                                            duration: {{ $objective->duration }},
                                            theory_session: '{{ $objective->theory_session }}',
                                            practical_session: '{{ $objective->practical_session }}',
                                            examination: '{{ $objective->examination }}',
                                            requirements: @js(clean_html($objective->requirement)),
                                            video_url: '{{ $objective->video_url }}'
                                        })" 
                                        aria-controls="edit-modal">
                                        Edit
                                    </button>
                                    <button class="btn border-red-200 dark:border-red-700/60 hover:border-red-300 dark:hover:border-red-600 text-red-800 dark:text-red-300 w-1/2" @click="$store.deleteModal.open({{ $objective->id }})" aria-controls="delete-modal">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>                    
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Contact Sales -->
        <section>
            <div class="px-5 py-3 bg-[linear-gradient(135deg,var(--tw-gradient-stops))] from-violet-500/[0.12] dark:from-violet-500/[0.24] to-violet-500/[0.04] rounded-lg text-center xl:text-left xl:flex xl:flex-wrap xl:justify-between xl:items-center">
                <div class="text-gray-800 dark:text-gray-100 font-semibold mb-2 xl:mb-0">Missing on an objective?</div>
                <button class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white" @click="$store.createModal.open()" aria-controls="create-modal">Add New</button>
            </div>
        </section>
        
    </div>

</div>

<!-- Create Course Modal -->
@include('components.modals.create-course-modal')

<!-- Edit Course Modal -->
@include('components.modals.edit-course-modal')

<!-- Delete Course Modal -->
@include('components.modals.delete-course-modal')