<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Testimonials;

class TestimonialVisibility extends Component
{
    public Testimonials $testimonial;
    public bool $website_visibility;

    public function mount(Testimonials $testimonial)
    {
        $this->testimonial = $testimonial;
        $this->website_visibility = (bool) $testimonial->website_visibility;
    }

    public function updatedWebsiteVisibility()
    {
        abort_unless(auth()->user()?->hasPermission('update_testimonials'), 403);

        $this->validate([
            'website_visibility' => 'boolean'
        ]);

        // Update the database
        $this->testimonial->update(['website_visibility' => $this->website_visibility]);

        // Dispatch event if needed
        $this->dispatch('TestimonialVisibilityUpdated', $this->testimonial->id, $this->website_visibility);
    }

    public function render()
    {
        return view('livewire.testimonial-visibility');
    }
}
