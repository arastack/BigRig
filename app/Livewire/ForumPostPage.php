<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ForumPost;

class ForumPostPage extends Component
{
    public $postId;
    public $post;

    public function mount($postId)
    {
        $this->postId = $postId;
        $this->post = ForumPost::with('user')->findOrFail($this->postId);
    }

    /**
     * Authors may delete their own posts; forum moderators (delete_forum) may delete any.
     */
    public function deletePost()
    {
        $post = ForumPost::findOrFail($this->postId);
        abort_unless($post->user_id === auth()->id() || auth()->user()->hasPermission('delete_forum'), 403);

        $post->comments()->delete();
        $post->delete();

        session()->flash('success', 'Post deleted.');

        return $this->redirectRoute('forum.list');
    }

    public function render()
    {
        $post = ForumPost::with('user')->findOrFail($this->postId);

        $popularPosts = ForumPost::with('user')
            ->withCount('comments')
            ->orderBy('comments_count', 'desc')
            ->take(5)
            ->get();

        $categories = ForumPost::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->orderBy('category')
            ->get()
            ->map(function ($item) {
                return (object) [
                    'slug' => \Illuminate\Support\Str::slug($item->category),
                    'name' => $item->category
                ];
            });

        return view('livewire.forum-post-page', [
            'post' => $post,
            'popularPosts' => $popularPosts,
            'categories' => $categories,
        ]);
    }
}
