<div class="mt-6">
    <!-- Comment form -->
    @can('create_forum')
    <form wire:submit.prevent="addComment">
        <div class="flex items-start space-x-3 mb-3">
            <x-portal.avatar :user="Auth::user()" />
            <div class="grow">
                <label for="comment" class="sr-only">Write a comment…</label>
                <textarea id="comment" wire:model.defer="commentBody" class="form-textarea w-full focus:border-gray-300" rows="4" placeholder="Write a comment…"></textarea>
                @error('commentBody') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="text-right mb-6">
            <button type="submit" class="btn-sm bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white whitespace-nowrap">Reply →</button>
        </div>
    </form>
    @endcan

    <!-- Comments -->
    <div class="mt-4">
        <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-4">Comments</h3>
        <ul class="space-y-5">
            @foreach ($comments as $comment)
            <li class="relative pl-9 space-y-5">
                <!-- Comment wrapper -->
                <div class="flex items-start">
                    <!-- Comment upvote (static for now, can be Livewire-powered later) -->
                    <div class="absolute top-0 left-0">
                        <button class="text-xs font-semibold text-left w-6 rounded-sm flex flex-col justify-center items-center text-gray-600 dark:text-gray-300 hover:text-violet-500 dark:hover:text-violet-500">
                            <svg class="inline-flex fill-gray-400 dark:fill-gray-500 mt-1.5 mb-1.5" width="12" height="6" xmlns="http://www.w3.org/2000/svg">
                                <path d="m0 6 6-6 6 6z" />
                            </svg>
                            <div>0</div>
                        </button>
                    </div>

                    <!-- Comment content -->
                    <div>
                        <!-- Comment text -->
                        <div class="grow text-sm text-gray-800 dark:text-gray-100 space-y-2 mb-2">
                            <p>{{ $comment->body }}</p>
                        </div>

                        <!-- Comment footer -->
                        <div class="flex flex-wrap text-xs">
                            <div class="flex items-center after:block after:content-['·'] last:after:content-[''] after:text-sm after:text-gray-400 dark:after:text-gray-600 after:px-2">
                                <a class="block mr-2" href="javascript:void(0)">
                                    <x-portal.avatar :user="$comment->user" size="h-6 w-6" />
                                </a>
                                <a class="font-medium text-violet-500 hover:text-violet-600 dark:hover:text-violet-400" href="javascript:void(0)">{{ $comment->user->firstName }} {{ $comment->user->lastName }}</a>
                            </div>
                            <div class="flex items-center after:block after:content-['·'] last:after:content-[''] after:text-sm after:text-gray-400 dark:after:text-gray-600 after:px-2">
                                <span class="text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            @if ($comment->user_id === auth()->id() || auth()->user()->hasPermission('delete_forum'))
                                <div class="flex items-center">
                                    <button type="button" wire:click="deleteComment({{ $comment->id }})" wire:confirm="Delete this comment?" class="font-medium text-red-600 hover:underline">Delete</button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </li>
            @endforeach
        </ul>

        @if ($comments->count() == 0)
            <p class="text-gray-500 dark:text-gray-400 text-center py-5">No comments yet. Be the first to comment!</p>
        @endif
    </div>
</div>
