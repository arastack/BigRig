
    <div class="px-4 sm:px-6 lg:px-8 py-8 md:py-0 w-full max-w-9xl mx-auto">

        <div class="xl:flex">

            <!-- Left + Middle content -->
            <div class="md:flex flex-1">

                <!-- Left content -->
                <x-community.forum-left-content :categories="$categories" />

                <!-- Middle content -->
                <div class="flex-1 md:ml-8 xl:mx-4 2xl:mx-8">
                    <div class="md:py-8">

                        <!-- Buttons group -->
                        <div class="mb-4">
                            <div class="w-full flex flex-wrap -space-x-px">
                                <button class="btn grow bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 text-violet-500 rounded-none first:rounded-l-lg last:rounded-r-lg">Popular</button>
                                <button class="btn grow bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:bg-gray-50 dark:hover:bg-gray-700/20 text-gray-600 dark:text-gray-300 rounded-none first:rounded-l-lg last:rounded-r-lg">Newest</button>
                                <button class="btn grow bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:bg-gray-50 dark:hover:bg-gray-700/20 text-gray-600 dark:text-gray-300 rounded-none first:rounded-l-lg last:rounded-r-lg">Following</button>
                            </div>
                        </div>

                        <!-- Post -->
                        <article class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-5">

                            <!-- Breadcrumbs -->
                            <div class="mb-2">
                                <ul class="inline-flex flex-wrap text-sm font-medium">
                                    <li class="flex items-center">
                                        <a class="text-gray-500 dark:text-gray-400 hover:text-violet-500 dark:hover:text-violet-500" href="{{ route('forum.list') }}">Home</a>
                                        <svg class="fill-current text-gray-400 dark:text-gray-500 mx-2" width="16" height="16" viewBox="0 0 16 16">
                                            <path d="M6.6 13.4L5.2 12l4-4-4-4 1.4-1.4L12 8z" />
                                        </svg>
                                    </li>
                                    <li class="flex items-center">
                                        <span class="text-gray-500 dark:text-gray-400">Discussion</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- Header -->
                            <header class="pb-4">
                                <!-- Title -->
                                <div class="flex items-start space-x-3 mb-3">
                                    <h2 class="text-2xl text-gray-800 dark:text-gray-100 font-bold">{{ $post->title }}</h2>

                                    <!-- Upvote button -->
                                    <div class="shrink-0">
                                        <button class="text-xs font-semibold text-center h-12 w-12 border border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 rounded-lg flex flex-col justify-center items-center">
                                            <svg class="inline-flex fill-gray-400 dark:fill-gray-500 mt-1.5 mb-1.5" width="12" height="6" xmlns="http://www.w3.org/2000/svg">
                                                <path d="m0 6 6-6 6 6z" />
                                            </svg>
                                            <div>{{ $post->votes_count }}</div>
                                        </button>
                                    </div>
                                </div>

                                <!-- Meta -->
                                <div class="flex flex-wrap text-sm">
                                    <div class="flex items-center after:block after:content-['·'] last:after:content-[''] after:text-sm after:text-gray-400 dark:after:text-gray-600 after:px-2">
                                        <span class="font-medium text-violet-500">{{ $post->user->firstName }} {{ $post->user->lastName }}</span>
                                    </div>
                                    <div class="flex items-center after:block after:content-['·'] last:after:content-[''] after:text-sm after:text-gray-400 dark:after:text-gray-600 after:px-2">
                                        <span class="text-gray-500">{{ $post->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="flex items-center after:block after:content-['·'] last:after:content-[''] after:text-sm after:text-gray-400 dark:after:text-gray-600 after:px-2">
                                        <span class="text-gray-500">💬 {{ $post->comments()->count() }} Comments</span>
                                    </div>
                                </div>
                            </header>

                            @if ($post->user_id === auth()->id() || auth()->user()?->hasPermission('delete_forum'))
                                <div class="mb-4 flex justify-end">
                                    <button type="button" wire:click="deletePost" wire:confirm="Delete this post and all its comments?" class="btn-ghost btn-xs text-red-600"><x-site.icon name="trash" class="h-4 w-4" /> Delete post</button>
                                </div>
                            @endif

                            <!-- Content -->
                            <div class="space-y-4 mb-6">
                                {!! nl2br(e($post->body)) !!}
                            </div>

                            <!-- Comment form -->
                            @auth
                            <livewire:forum-comments :post-id="$post->id" />
                            @else
                            <p class="text-center text-gray-500 dark:text-gray-400">Please <a href="{{ route('login') }}" class="text-violet-500">login</a> to add a comment.</p>
                            @endauth

                        </article>
                    

                    </div>
                </div>

            </div>

            <!-- Right content -->
            <x-community.forum-right-content :popularPosts="$popularPosts" />

        </div>

    </div>
