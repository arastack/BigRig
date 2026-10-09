<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutCompany;
use App\Models\Achievements;
use App\Models\Clients;
use App\Models\Faqs;
use App\Models\Founder;
use App\Models\Services;
use App\Models\Slider;
use App\Models\Testimonials;
use App\Models\TrainingObjective;
use Illuminate\Http\Request;

/**
 * One place to reach every editor for the public website.
 */
class ContentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $sections = collect([
            [
                'title' => 'About the school',
                'text' => 'The story, mission and vision shown on the About page and homepage.',
                'icon' => 'building',
                'route' => 'about-company',
                'count' => AboutCompany::exists() ? 'Set up' : 'Not set up',
                'site' => route('about-us'),
            ],
            [
                'title' => 'Founder’s message',
                'text' => 'The founder’s photo, name and welcome message.',
                'icon' => 'quote',
                'route' => 'founder',
                'count' => Founder::exists() ? 'Set up' : 'Not set up',
                'site' => route('about-us'),
            ],
            [
                'title' => 'Homepage sliders',
                'text' => 'The large rotating images and headlines at the top of the homepage.',
                'icon' => 'play',
                'route' => 'sliders',
                'count' => $this->plural(Slider::count(), 'slide'),
                'site' => route('index'),
            ],
            [
                'title' => 'Services',
                'text' => 'Extra services the school offers, listed on the homepage, About and Courses pages.',
                'icon' => 'wrench',
                'route' => 'custom-services',
                'count' => $this->plural(Services::count(), 'service'),
                'site' => route('courses'),
            ],
            [
                'title' => 'FAQs',
                'text' => 'Common questions and answers for people thinking of applying.',
                'icon' => 'message',
                'route' => 'faqs',
                'count' => $this->plural(Faqs::count(), 'question'),
                'site' => route('faq'),
            ],
            [
                'title' => 'Partners & clients',
                'text' => 'Logos of the companies the school works with, shown on the homepage.',
                'icon' => 'briefcase',
                'route' => 'clients',
                'count' => $this->plural(Clients::count(), 'logo'),
                'site' => route('index'),
            ],
            [
                'title' => 'Achievements',
                'text' => 'Milestones by year, shown on the About page.',
                'icon' => 'award',
                'route' => 'achievements',
                'count' => $this->plural(Achievements::count(), 'achievement'),
                'site' => route('about-us'),
            ],
            [
                'title' => 'Testimonials',
                'text' => 'What students say about the school. Choose which ones appear on the website.',
                'icon' => 'star',
                'route' => 'testimonials',
                'can' => 'read_testimonials',
                'count' => fn () => $this->plural(Testimonials::count(), 'testimonial') . ' · ' . Testimonials::where('website_visibility', true)->count() . ' on the site',
                'site' => route('index'),
            ],
            [
                'title' => 'Courses & fees',
                'text' => 'The courses you offer, with their duration, price and curriculum.',
                'icon' => 'truck',
                'route' => 'objectives',
                'can' => 'manage_course_catalogue',
                'count' => fn () => $this->plural(TrainingObjective::count(), 'course'),
                'site' => route('courses'),
            ],
        ])
            ->filter(fn ($section) => empty($section['can']) || $user->hasPermission($section['can']))
            ->map(fn ($section) => ['count' => value($section['count'])] + $section)
            ->values();

        return view('admin.content.index', ['sections' => $sections]);
    }

    private function plural(int $count, string $word): string
    {
        return number_format($count) . ' ' . str($word)->plural($count);
    }
}
