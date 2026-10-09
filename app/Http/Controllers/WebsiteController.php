<?php

namespace App\Http\Controllers;

use App\Models\AboutCompany;
use App\Models\Achievements;
use App\Models\Clients;
use App\Models\ContactMessage;
use App\Models\EmailSubscription;
use App\Models\Faqs;
use App\Models\Founder;
use App\Models\Role;
use App\Models\Services;
use App\Models\Settings;
use App\Models\Slider;
use App\Models\Testimonials;
use App\Models\TrainingObjective;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class WebsiteController extends Controller
{
    public function home()
    {
        return view('site.home', [
            'sliders' => Slider::orderBy('id')->get(),
            'about' => AboutCompany::first(),
            'founder' => Founder::first(),
            'courses' => TrainingObjective::orderBy('price')->get(),
            'services' => Services::orderBy('id')->get(),
            'partners' => Clients::whereNotNull('logo')->orderBy('id')->get(),
            'testimonials' => Testimonials::where('website_visibility', true)->latest()->take(9)->get(),
            'team' => $this->team()->take(4)->get(),
            'faqs' => Faqs::orderBy('id')->take(5)->get(),
        ]);
    }

    /**
     * Kept for the old "preferred landing page" setting; both variants now share one design.
     */
    public function index()
    {
        return $this->home();
    }

    public function about()
    {
        return view('site.about', [
            'about' => AboutCompany::first(),
            'founder' => Founder::first(),
            'achievements' => Achievements::orderByDesc('year')->get(),
            'services' => Services::orderBy('id')->get(),
            'team' => $this->team()->take(4)->get(),
        ]);
    }

    public function instructors()
    {
        return view('site.instructors', [
            'team' => $this->team()->get(),
            'about' => AboutCompany::first(),
        ]);
    }

    public function courses()
    {
        return view('site.courses', [
            'courses' => TrainingObjective::orderBy('price')->get(),
            'services' => Services::orderBy('id')->get(),
        ]);
    }

    public function course_details($name)
    {
        $course = TrainingObjective::findBySlug($name) ?? abort(404);

        // Old links used a different slug format; send them to the canonical URL
        if ($name !== $course->slug) {
            return redirect()->route('course', $course->slug, 301);
        }

        return view('site.course', [
            'course' => $course->load('curriculum'),
            'otherCourses' => TrainingObjective::where('id', '!=', $course->id)->orderBy('price')->get(),
            'faqs' => Faqs::orderBy('id')->take(4)->get(),
        ]);
    }

    public function faq()
    {
        return view('site.faq', ['faqs' => Faqs::orderBy('id')->get()]);
    }

    public function contact()
    {
        return view('site.contact', ['courses' => TrainingObjective::orderBy('objective')->get()]);
    }

    public function contactStore(Request $request)
    {
        // Honeypot: real visitors never fill this hidden field
        if (filled($request->input('website'))) {
            return redirect()->route('contact')->with('success', 'Thank you — your message has been sent.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:190',
            'phone' => 'nullable|string|max:30',
            'course_id' => 'nullable|exists:training_objectives,id',
            'subject' => 'nullable|string|max:190',
            'message' => 'required|string|min:10|max:5000',
        ]);

        $message = ContactMessage::create($data + ['ip_address' => $request->ip()]);

        $this->notifySchool($message);

        return redirect()->route('contact')->with('success', 'Thank you, ' . $message->name . '. Your message has been sent — our admissions team will get back to you shortly.');
    }

    public function privacy()
    {
        return view('site.privacy');
    }

    public function terms()
    {
        return view('site.terms');
    }

    public function email_subscription(Request $request)
    {
        $request->validateWithBag('newsletter', ['email' => 'required|email|max:190']);

        $created = EmailSubscription::firstOrCreate(['email' => strtolower($request->email)])->wasRecentlyCreated;

        return redirect()->to(url()->previous() . '#newsletter')->with(
            'newsletter',
            $created ? 'You are subscribed. Watch your inbox for intake dates and news.' : 'You are already subscribed to our newsletter.'
        );
    }

    /**
     * Staff the school has chosen to show on the website.
     */
    private function team()
    {
        return User::with('roles')
            ->where('user_active', true)
            ->whereNull('suspended_at')
            ->where('website_visibility', true)
            ->whereHas('roles', fn ($q) => $q->where('portal', '!=', Role::PORTAL_STUDENT))
            ->orderBy('firstName');
    }

    private function notifySchool(ContactMessage $message): void
    {
        $to = Settings::first()?->business_email;
        if (! $to) {
            return;
        }

        after_response(fn () => Mail::raw(
                "New enquiry from the website\n\n"
                . "Name: {$message->name}\nEmail: {$message->email}\nPhone: {$message->phone}\n"
                . 'Course: ' . ($message->course?->objective ?? '—') . "\n\n{$message->message}",
            fn ($mail) => $mail->to($to)->replyTo($message->email, $message->name)->subject('Website enquiry: ' . ($message->subject ?: $message->name))
        ), 'Contact notification email');
    }
}
