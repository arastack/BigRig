<div class="space-y-8">
    <!-- Alert -->
    <div class="relative bg-[linear-gradient(135deg,var(--tw-gradient-stops))] from-violet-500/[0.12] dark:from-violet-500/[0.24] to-violet-500/[0.04] rounded-lg p-5">
        <div class="absolute bottom-0 -mb-3">
            <svg width="44" height="42" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <defs>
                    <linearGradient x1="50%" y1="0%" x2="50%" y2="100%" id="ill-b">
                        <stop stop-color="#B7ACFF" offset="0%" />
                        <stop stop-color="#9C8CFF" offset="100%" />
                    </linearGradient>
                    <linearGradient x1="50%" y1="24.537%" x2="50%" y2="100%" id="ill-c">
                        <stop stop-color="#4634B1" offset="0%" />
                        <stop stop-color="#4634B1" stop-opacity="0" offset="100%" />
                    </linearGradient>
                    <path id="ill-a" d="m20 0 20 40-20-6.25L0 40z" />
                </defs>
                <g transform="scale(-1 1) rotate(-51 -11.267 67.017)" fill="none" fill-rule="evenodd">
                    <mask id="ill-d" fill="#fff">
                        <use xlink:href="#ill-a" />
                    </mask>
                    <use fill="url(#ill-b)" xlink:href="#ill-a" />
                    <path fill="url(#ill-c)" mask="url(#ill-d)" d="M20.586-7.913h25v47.5h-25z" />
                </g>
            </svg>
        </div>
        <div class="relative">
            <div class="text-sm font-medium text-gray-800 dark:text-violet-200 mb-2">Remember to keep track of your courses.</div>
            <div class="text-right">
                <a class="text-sm font-medium text-violet-500 hover:text-violet-600" href="javascript:void(0)" @click="$store.createModal.open()" aria-controls="create-modal">Create New Course -&gt;</a>
            </div>
        </div>
    </div>
    <!-- White box -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-5 min-w-60">
        <div class="grid md:grid-cols-1 xl:grid-cols-1 gap-6">
            <!-- Group 1 -->
            <div>
                <h2 class="text-gray-800 dark:text-gray-100 font-semibold mb-2">Course List</h2>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                    <ul class="space-y-3">
                        @if ($objectives->isEmpty())
                            <div class="col-span-full bg-transparent dark:bg-transparent shadow-sm rounded-xl">
                                <div class="flex flex-col h-full text-center p-5">
                                    <div class="grow mb-1">
                                        <h3 class="text-lg text-gray-800 dark:text-gray-100 font-semibold mb-1">No record found.</h3>
                                    </div>
                                </div>
                            </div>
                        @else
                            @foreach($objectives as $objective)
                            <!-- Item -->
                            <li class="sm:flex sm:items-center sm:justify-between border-b border-gray-200 dark:border-gray-700/60 p-2">
                                <div class="sm:grow flex items-center text-sm">
                                    <!-- Icon -->
                                    <div class="w-12 h-12 shrink-0 my-2 mr-3">
                                        <img class="w-full h-12" src="{{ Storage::url($objective->image_url) }}" width="20" height="20" alt="{{ $objective->objective }}" />
                                    </div>
                                    <!-- Position -->
                                    <a href="{{ route('course-details', ['id' => $objective->id]) }}">
                                        <div class="font-medium text-gray-800 dark:text-gray-100">{{ $objective->objective }}</div>
                                        <div class="flex flex-nowrap items-center space-x-2 whitespace-nowrap">
                                            <div>Duration: {{ $objective->duration }} weeks</div>
                                            <div class="text-gray-400 dark:text-gray-600">|</div>
                                            <div>Cost: {{ $settings->base_currency }}{{ number_format($objective->price, 2) }}</div>
                                        </div>
                                    </a>
                                </div>
                                <!-- Tags -->
                                <div class="sm:ml-2 mt-2 sm:mt-0">
                                    <div class="flex items-center space-x-2">
                                        <!-- Edit Button -->
                                        <button type="button"  @click="$store.editModal.open({
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
                                            aria-controls="edit-modal" class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600">
                                            <svg class="fill-current text-gray-400 dark:text-gray-500 shrink-0" width="16" height="16" viewBox="0 0 16 16">
                                                <path d="M11.7.3c-.4-.4-1-.4-1.4 0l-10 10c-.2.2-.3.4-.3.7v4c0 .6.4 1 1 1h4c.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4l-4-4zM4.6 14H2v-2.6l6-6L10.6 8l-6 6zM12 6.6L9.4 4 11 2.4 13.6 5 12 6.6z" />
                                            </svg>
                                        </button>

                                        <!-- Delete Button -->
                                        <button type="button" @click="$store.deleteModal.open({{ $objective->id }})" aria-controls="delete-modal" class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600" aria-controls="delete-modal">
                                            <svg class="fill-current text-red-500 shrink-0" width="16" height="16" viewBox="0 0 16 16">
                                                <path d="M5 7h2v6H5V7zm4 0h2v6H9V7zm3-6v2h4v2h-1v10c0 .6-.4 1-1 1H2c-.6 0-1-.4-1-1V5H0V3h4V1c0-.6.4-1 1-1h6c.6 0 1 .4 1 1zM6 2v1h4V2H6zm7 3H3v9h10V5z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Create Course Modal -->
@include('components.modals.create-course-modal')

<!-- Edit Course Modal -->
@include('components.modals.edit-course-modal')

<!-- Delete Objective Modal -->
@include('components.modals.delete-course-modal')

