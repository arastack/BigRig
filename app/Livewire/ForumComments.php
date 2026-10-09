<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ForumComment;

class ForumComments extends Component
{
    public $postId;
    public $commentBody;

    public function mount($postId)
    {
        $this->postId = $postId;
    }

    public function addComment()
    {
        abort_unless(auth()->user()->hasPermission('create_forum'), 403);

        $this->validate([
            'commentBody' => 'required|min:3',
        ]);

        ForumComment::create([
            'forum_post_id' => $this->postId,
            'user_id' => auth()->id(),
            'body' => $this->commentBody,
        ]);

        $this->commentBody = '';
    }

    public function deleteComment(int $commentId)
    {
        $comment = ForumComment::where('forum_post_id', $this->postId)->findOrFail($commentId);
        abort_unless($comment->user_id === auth()->id() || auth()->user()->hasPermission('delete_forum'), 403);

        $comment->delete();
    }

    public function render()
    {
        $comments = ForumComment::where('forum_post_id', $this->postId)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('livewire.forum-comments', ['comments' => $comments]);
    }
}
