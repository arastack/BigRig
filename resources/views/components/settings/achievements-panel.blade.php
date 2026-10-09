<div class="grow">

    <!-- Panel body -->
    <div class="p-6 flex space-x-4">
        <section class="grid xl:grid-cols-1 gap-6 mb-8">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-xl leading-snug text-gray-800 dark:text-gray-100 font-bold mb-1">Achievements So Far</h3>
                <button 
                    @click="$store.createModal.open()" aria-controls="create-modal"
                    class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 ml-auto">
                    <svg class="fill-current text-violet-500 shrink-0" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                    </svg>
                </button>
            </div>

            @if ($achievements->isEmpty())
                <div class="col-span-full bg-white dark:bg-gray-800 shadow-sm rounded-xl">
                    <div class="flex flex-col h-full text-center p-5">
                        <div class="grow mb-1">
                            <h3 class="text-lg text-gray-800 dark:text-gray-100 font-semibold mb-1">No record found.</h3>
                        </div>
                    </div>
                </div>
            @else
                @foreach ($achievements as $achievement)
                    <article class="flex bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden">
                        <!-- Image -->
                        <a class="relative block w-24 sm:w-56 xl:sidebar-expanded:w-40 2xl:sidebar-expanded:w-56 shrink-0" href="javascript:void(0)">
                            <img class="absolute object-cover object-center w-full h-full" src="{{ Storage::url($achievement->picture) }}" width="220" height="236" alt="{{ $achievement->title }}" />
                        </a>
                        <!-- Content -->
                        <div class="grow p-5 flex flex-col">
                            <div class="grow">
                                <div class="text-sm font-semibold text-violet-500 uppercase mb-2">{{ $achievement->year }}</div>
                                <a class="inline-flex mb-2" href="javascript:void(0)">
                                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ $achievement->title }}</h3>
                                </a>
                                <div class="text-sm">{!! clean_html($achievement->description) !!}</div>
                            </div>
                            <!-- Footer -->
                            <div class="flex justify-between items-center mt-3">
                                <!-- Tag -->
                                <div class="text-xs inline-flex items-center font-medium border border-gray-200 dark:border-gray-700/60 text-gray-600 dark:text-gray-400 rounded-full text-center px-2.5 py-1">
                                    <svg class="w-4 h-3 fill-gray-400 dark:fill-gray-500 mr-2" viewBox="0 0 16 12">
                                        <path d="m16 2-4 2.4V2a2 2 0 0 0-2-2H2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7.6l4 2.4V2ZM2 10V2h8v8H2Z" />
                                    </svg>
                                </div>
                                <!-- Avatars -->
                                <div class="flex items-center space-x-2">
                                    <!-- Edit Button -->
                                    <button type="button" @click="$store.editModal.open({
                                        id: {{ $achievement->id }},
                                        title: '{{ $achievement->title }}',
                                        year: '{{ $achievement->year }}',
                                        description: '{{ $achievement->description }}',
                                    })" class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600">
                                        <svg class="fill-current text-gray-400 dark:text-gray-500 shrink-0" width="16" height="16" viewBox="0 0 16 16">
                                            <path d="M11.7.3c-.4-.4-1-.4-1.4 0l-10 10c-.2.2-.3.4-.3.7v4c0 .6.4 1 1 1h4c.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4l-4-4zM4.6 14H2v-2.6l6-6L10.6 8l-6 6zM12 6.6L9.4 4 11 2.4 13.6 5 12 6.6z" />
                                        </svg>
                                    </button>

                                    <!-- Delete Button -->
                                    <button type="button" class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600" @click="$store.deleteModal.open({{ $achievement->id }})" aria-controls="delete-modal">
                                        <svg class="fill-current text-red-500 shrink-0" width="16" height="16" viewBox="0 0 16 16">
                                            <path d="M5 7h2v6H5V7zm4 0h2v6H9V7zm3-6v2h4v2h-1v10c0 .6-.4 1-1 1H2c-.6 0-1-.4-1-1V5H0V3h4V1c0-.6.4-1 1-1h6c.6 0 1 .4 1 1zM6 2v1h4V2H6zm7 3H3v9h10V5z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            @endif

        </section>
    </div>
</div>

<!-- Create Achievement Modal -->
@include('components.modals.create-achievement-modal')

<!-- Edit Achievement Modal -->
@include('components.modals.edit-achievement-modal')

<!-- Delete Achievement Modal -->
@include('components.modals.delete-achievement-modal')