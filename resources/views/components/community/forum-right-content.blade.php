<div class="w-full hidden xl:block xl:w-72">
    <div class="lg:sticky lg:top-16 lg:h-[calc(100dvh-64px)] lg:overflow-x-hidden lg:overflow-y-auto no-scrollbar">
        <div class="md:py-8">

            <!-- Create Post Button -->
            @can('create_forum')
                <div class="mb-6">
                    <a href="{{ route('forum.create') }}" class="btn-brand w-full">Create post</a>
                </div>
            @endcan

            <!-- Blocks -->
            <div class="space-y-4">
                
                <!-- Block 2: Popular Stories -->
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl">
                    <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase mb-4">Popular Stories</div>
                    <ul class="space-y-3">
                        @foreach ($popularPosts as $popularPost)
                        <li>
                            <div class="text-sm mb-1">
                                <a class="font-medium text-gray-800 hover:text-gray-900 dark:text-gray-100 dark:hover:text-white" href="{{ route('forum.post', $popularPost->id) }}">{{ $popularPost->title }}</a>
                            </div>
                            <div class="text-xs text-gray-500">
                                <a class="font-medium text-violet-500 hover:text-violet-600 dark:hover:text-violet-400" href="javascript:void(0)">
                                    {{ $popularPost->user->firstName }} {{ $popularPost->user->lastName }}
                                </a> · {{ $popularPost->created_at->diffForHumans() }} · {{ $popularPost->comments_count }} comments
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    <div class="mt-4">
                        <a href="{{ route('forum.list') }}">
                            <button class="btn-sm w-full bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-800 dark:text-gray-300">View All</button>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
