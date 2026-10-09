<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ForumPost;

class ForumList extends Component
{
    public $search = '';
    public $category = null;

    public function mount($category = null)
    {
        $this->category = $category ? str_replace('-', ' ', $category) : null;
    }

    public function render()
    {
        $posts = ForumPost::with('user')
            ->when($this->category, function ($query) {
                $query->where('category', $this->category);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
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

        return view('livewire.forum-list', [
            'posts' => $posts,
            'popularPosts' => $popularPosts,
            'categories' => $categories,
        ]);
    }
}
