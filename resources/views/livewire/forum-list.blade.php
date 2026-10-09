
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

                        <!-- Forum entries -->
                        <div class="space-y-2">
                            <x-community.forum-entries :posts="$posts" />
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6 text-right">
                            <nav class="inline-flex" role="navigation" aria-label="Navigation">
                                <ul class="flex justify-center">
                                    <li class="ml-3 first:ml-0">
                                        <span class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 text-gray-300 dark:text-gray-600">&lt;- Previous</span>
                                    </li>
                                    <li class="ml-3 first:ml-0">
                                        <a class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-800 dark:text-gray-300" href="javascript:void(0)">Next -&gt;</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>

                    </div>
                </div>

            </div>

            <!-- Right content -->
            <x-community.forum-right-content :popularPosts="$popularPosts" />

        </div>

    </div>
