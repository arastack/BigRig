<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;

class WebsiteVisibility extends Component
{
    public User $user;
    public bool $website_visibility;

    public function mount(User $user)
    {
        $this->user = $user;
        $this->website_visibility = (bool) $user->website_visibility;
    }

    public function updatedWebsiteVisibility()
    {
        abort_unless(auth()->user()?->hasPermission('update_instructors'), 403);

        $this->validate([
            'website_visibility' => 'boolean'
        ]);

        // Update the database
        $this->user->update(['website_visibility' => $this->website_visibility]);

        // Refresh user model to reflect changes
        $this->user->refresh();

        // Dispatch event if needed
        $this->dispatch('websiteVisibilityUpdated', $this->user->id, $this->website_visibility);
    }

    public function render()
    {
        return view('livewire.website-visibility');
    }
}
