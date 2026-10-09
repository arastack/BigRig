<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatInterface extends Component
{
    public $selectedUserId = null;
    public $searchQuery = '';
    public $users = [];

    protected $listeners = ['messagesRead' => 'handleMessagesRead'];

    public function mount()
    {
        abort_unless(Auth::user()?->hasPermission('read_chats'), 403);

        $this->search(); // Load default user list
    }

    public function selectUser($userId)
    {
        $this->selectedUserId = $userId;
    }

    public function handleMessagesRead($userId)
    {
        $this->search(); // Refresh users list
    }

    public function search()
    {
        $currentUserId = Auth::id();
        $search = trim($this->searchQuery);

        $query = User::where('user_active', 1)
            ->where('id', '!=', $currentUserId);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('firstName', 'like', '%' . $search . '%')
                  ->orWhere('lastName', 'like', '%' . $search . '%')
                  ->orWhere('middleName', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere(DB::raw("REPLACE(mobileNumber, ' ', '')"), 'like', '%' . str_replace(' ', '', $search) . '%');
            });
        }

        $users = $query->get()->map(function ($user) use ($currentUserId) {
            $lastMessage = Message::where(function ($q) use ($user, $currentUserId) {
                    $q->where('sender_id', $currentUserId)
                      ->where('receiver_id', $user->id);
                })->orWhere(function ($q) use ($user, $currentUserId) {
                    $q->where('sender_id', $user->id)
                      ->where('receiver_id', $currentUserId);
                })->latest('created_at')->first();

            $unreadCount = Message::where('sender_id', $user->id)
                ->where('receiver_id', $currentUserId)
                ->where('is_read', false)
                ->count();

            return [
                'id' => $user->id,
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'profile_photo_path' => $user->profile_photo_path,
                'unread' => $unreadCount,
                'last_message_time' => optional($lastMessage)->created_at,
                'last_message_preview' => optional($lastMessage)->body,
                'last_message_time_human' => optional($lastMessage)?->created_at?->diffForHumans(),
                'has_messages' => $lastMessage !== null,
            ];
        });

        if (!$search) {
            $users = $users->filter(fn($u) => $u['has_messages']);
        }

        $this->users = $users->sortByDesc('last_message_time')->values()->toArray();
    }

    public function render()
    {
        return view('livewire.chat-interface', [
            'users' => $this->users,
            'selectedUserId' => $this->selectedUserId
        ]);
    }
}
