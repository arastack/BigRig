<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Settings;
use App\Models\TrainingObjective;
use App\Models\Faqs;
use App\Models\Slider;
use App\Models\Founder;
use App\Models\Services;
use App\Models\Clients;
use App\Models\AboutCompany;
use App\Models\Achievements;

class SettingsController extends Controller
{
    /**
     * siteInformationIndex
     *
     * @return void
     */
    public function siteInformationIndex()
    {
        // $settings = Settings::first();
        return view('pages/settings/site_information');
    }

    /**
     * siteInformationUpdate
     *
     * @param  mixed $request
     * @return void
     */
    public function siteInformationUpdate(Request $request)
    {
        $request->validate([
            'dark_theme_logo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048', // Max file size 2MB
            'light_theme_logo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048', // Max file size 2MB
            'favicon' => 'nullable|mimes:jpg,png,jpeg,ico|max:1024', // Remove 'image'
            'site_name' => 'required|string|max:255',
            'site_tagline' => 'nullable|string',
            'commence_year' => 'required|digits:4|integer|min:1900|max:' . date('Y'),
            'site_description' => 'required|string',
            'site_keywords' => 'required|string',
            'headquarters' => 'required|string',
            'business_email' => 'required|email',
            'secondary_email' => 'nullable|email',
            'business_contact' => 'required|string|max:20',
            'secondary_contact' => 'nullable|string|max:20',
            'whatsapp_support' => 'nullable|string|max:20',
            'telegram_support' => 'nullable|string|max:20',
            'base_currency' => 'required|string|max:1',
            'facebook_handle' => 'nullable|string',
            'twitter_handle' => 'nullable|string',
            'instagram_handle' => 'nullable|string',
            'youtube_handle' => 'nullable|string',
            'tiktok_handle' => 'nullable|string',
            'linkedin_handle' => 'nullable|string',
            'show_whatsapp_support' => 'sometimes|boolean',
            'show_telegram_support' => 'sometimes|boolean',
            'show_preloader' => 'sometimes|boolean',
            'preferred_landing_page' => 'sometimes|integer|in:1,2'
        ]);

        $settings = Settings::first(); // Assuming only one settings record exists
        $old_dark_logo = $settings->dark_theme_logo;
        $old_light_logo = $settings->light_theme_logo;
        $old_favicon = $settings->favicon;

        // Handle dark_theme_logo upload
        if ($request->hasFile('dark_theme_logo')) {
            $darkThemeFilePath = $request->file('dark_theme_logo')->store('logo', 'public');
            $settings->dark_theme_logo = $darkThemeFilePath;

            if ($old_dark_logo && Storage::disk('public')->exists($old_dark_logo)) {
                Storage::disk('public')->delete($old_dark_logo);
            }
        }

        // Handle light_theme_logo upload
        if ($request->hasFile('light_theme_logo')) {
            $lightThemeFilePath = $request->file('light_theme_logo')->store('logo', 'public');
            $settings->light_theme_logo = $lightThemeFilePath;

            if ($old_light_logo && Storage::disk('public')->exists($old_light_logo)) {
                Storage::disk('public')->delete($old_light_logo);
            }
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            $faviconFilePath = $request->file('favicon')->store('logo', 'public');
            $settings->favicon = $faviconFilePath;

            if ($old_favicon && Storage::disk('public')->exists($old_favicon)) {
                Storage::disk('public')->delete($old_favicon);
            }
        }

        // Update other fields
        $settings->site_name = $request->input('site_name');
        $settings->site_tagline = $request->input('site_tagline');
        $settings->commence_year = $request->input('commence_year');
        $settings->site_description = $request->input('site_description');
        $settings->site_keywords = $request->input('site_keywords');
        $settings->headquarters = $request->input('headquarters');
        $settings->business_email = $request->input('business_email');
        $settings->secondary_email = $request->input('secondary_email');
        $settings->business_contact = $request->input('business_contact');
        $settings->secondary_contact = $request->input('secondary_contact');
        $settings->whatsapp_support = $request->input('whatsapp_support');
        $settings->telegram_support = $request->input('telegram_support');
        $settings->base_currency = $request->input('base_currency');
        $settings->facebook_handle = $request->input('facebook_handle');
        $settings->twitter_handle = $request->input('twitter_handle');
        $settings->instagram_handle = $request->input('instagram_handle');
        $settings->youtube_handle = $request->input('youtube_handle');
        $settings->tiktok_handle = $request->input('tiktok_handle');
        $settings->linkedin_handle = $request->input('linkedin_handle');
        $settings->show_whatsapp_support = $request->input('show_whatsapp_support');
        if ($request->has('show_telegram_support')) {
            $settings->show_telegram_support = $request->input('show_telegram_support');
        }
        if ($request->has('show_preloader')) {
            $settings->show_preloader = $request->input('show_preloader');
        }
        if ($request->has('preferred_landing_page')) {
            $settings->preferred_landing_page = $request->input('preferred_landing_page');
        }
        // Update other fields as needed

        $settings->save();

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }

    public function aboutCompanyIndex()
    {
        $about = AboutCompany::first();
        return view('pages/settings/about', compact('about'));
    }

    /**
     * aboutCompanyUpdate
     *
     * @param  mixed $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function aboutCompanyUpdate(Request $request)
    {
        // Validate the request data
        $request->validate([
            'banner_title' => 'required|string|max:255',
            'banner_picture' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'history_title' => 'required|string|max:255',
            'training_hours' => 'nullable|numeric',
            'company_history' => 'required|string',
            'mission_statement' => 'required|string',
            'students_count' => 'nullable|numeric',
            'years_of_existence' => 'nullable|numeric',
            'instructors_count' => 'nullable|numeric',
            'pass_rate' => 'required|numeric|between:0,100',
        ]);

        // Retrieve the existing record or create a new one
        $aboutCompany = AboutCompany::firstOrCreate([], []);

        // Handle banner picture upload
        if ($request->hasFile('banner_picture')) {
            // Delete old banner picture if exists
            if ($aboutCompany->banner_picture && Storage::disk('public')->exists($aboutCompany->banner_picture)) {
                Storage::disk('public')->delete($aboutCompany->banner_picture);
            }
            $aboutCompany->banner_picture = $request->file('banner_picture')->store('banners', 'public');
        }

        // Update other fields
        $aboutCompany->update([
            'banner_title' => $request->input('banner_title'),
            'history_title' => $request->input('history_title'),
            'training_hours' => $request->input('training_hours'),
            'company_history' => $request->input('company_history'),
            'mission_statement' => $request->input('mission_statement'),
            'students_count' => $request->input('students_count'),
            'years_of_existence' => $request->input('years_of_existence'),
            'instructors_count' => $request->input('instructors_count'),
            'pass_rate' => $request->input('pass_rate'),
        ]);

        return redirect()->back()->with('success', 'About Company information updated successfully!');
    }

    public function trainingObjectiveIndex()
    {
        // Retrieve all training objectives in a specific order (e.g., by `id`)
        $objectives = TrainingObjective::orderBy('price', 'asc')->get();

        // Pass the data to the view
        return view('pages.settings.objectives', compact('objectives'));
    }

    public function trainingObjectiveStore(Request $request)
    {
        $request->validate([
            'objective' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'theory_session' => 'required|integer|min:1',
            'practical_session' => 'required|integer|min:1',
            'examination' => 'required|string',
            'requirements' => 'required|string',
            'video_url' => 'nullable|url',
            'image_url' => 'required|image|mimes:jpg,png,jpeg|max:2048', // Max file size 2MB
            'video_thumbnail_url' => 'nullable|image|mimes:jpg,png,jpeg|max:2048', // Max file size 2MB
        ],
        [
            'image_url.image' => 'Display Picture: The uploaded file must be an image.',
            'image_url.mimes' => 'Display Picture: Only JPG and PNG file types are allowed.',
            'image_url.max' => 'Display Picture: The image size must not exceed 2 MB.',
            'video_thumbnail_url.image' => 'Video Thumbnail: The uploaded file must be an image.',
            'video_thumbnail_url.mimes' => 'Video Thumbnail: Only JPG and PNG file types are allowed.',
            'video_thumbnail_url.max' => 'Video Thumbnail: The image size must not exceed 2 MB.',
        ]);

        // Handle image_url upload
        $filePath = null;
        if ($request->hasFile('image_url')) {
            $filePath = $request->file('image_url')->store('courses', 'public');
        }

        // Handle video_thumbnail_url upload
        $filePath2 = null;
        if ($request->hasFile('video_thumbnail_url')) {
            $filePath2 = $request->file('video_thumbnail_url')->store('courses', 'public');
        }

        TrainingObjective::create([
            'objective' => $request->input('objective'),
            'duration' => $request->input('duration'),
            'theory_session' => $request->input('theory_session'),
            'practical_session' => $request->input('practical_session'),
            'examination' => $request->input('examination'),
            'price' => $request->input('price'),
            'requirement' => $request->input('requirements'),
            'image_url' => $filePath,
            'video_thumbnail_url' => $filePath2, // This will now be null if no file is uploaded
            'video_url' => $request->input('video_url'),
        ]);

        return redirect()->back()->with('success', 'Objective created successfully.');
    }

    public function trainingObjectiveUpdate(Request $request)
    {
        $request->validate(
            [
                'id' => 'required|exists:training_objectives,id',
                'objective' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'duration' => 'required|integer|min:1',
                'theory_session' => 'required|integer|min:1',
                'practical_session' => 'required|integer|min:1',
                'examination' => 'required|string',
                'requirements' => 'required|string',
                'video_url' => 'nullable|url',
                'image_url' => 'nullable|image|mimes:jpg,png,jpeg|max:2048', // Optional
                'video_thumbnail_url' => 'nullable|image|mimes:jpg,png,jpeg|max:2048', // Optional
            ],
            [
                'image_url.image' => 'Display Picture: The uploaded file must be an image.',
                'image_url.mimes' => 'Display Picture: Only JPG and PNG file types are allowed.',
                'image_url.max' => 'Display Picture: The image size must not exceed 2 MB.',
                'video_thumbnail_url.image' => 'Video Thumbnail: The uploaded file must be an image.',
                'video_thumbnail_url.mimes' => 'Video Thumbnail: Only JPG and PNG file types are allowed.',
                'video_thumbnail_url.max' => 'Video Thumbnail: The image size must not exceed 2 MB.',
            ]
        );

        $objective = TrainingObjective::findOrFail($request->input('id'));

        // Update fields
        $objective->objective = $request->input('objective');
        $objective->price = $request->input('price');
        $objective->duration = $request->input('duration');
        $objective->theory_session = $request->input('theory_session');
        $objective->practical_session = $request->input('practical_session');
        $objective->examination = $request->input('examination');
        $objective->requirement = $request->input('requirements');
        $objective->video_url = $request->input('video_url');

        // Handle image upload
        if ($request->hasFile('image_url')) {
            $oldImagePath = $objective->image_url;
            $filePath = $request->file('image_url')->store('courses', 'public');

            $objective->image_url = $filePath;

            if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }
        }

        if ($request->hasFile('video_thumbnail_url')) {
            $oldImagePath = $objective->video_thumbnail_url;
            $filePath = $request->file('video_thumbnail_url')->store('courses', 'public');

            $objective->video_thumbnail_url = $filePath;

            if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }
        }

        $objective->save();

        return redirect()->back()->with('success', 'Objective updated successfully!');
    }

    public function trainingObjectiveDestroy($id)
    {
        $objective = TrainingObjective::findOrFail($id);
        $filePath = $objective->image_url;
        $filePath2 = $objective->video_thumbnail_url;
        $objective->delete();

        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }

        if ($filePath2 && Storage::disk('public')->exists($filePath2)) {
            Storage::disk('public')->delete($filePath2);
        }

        return redirect()->back()->with('success', 'Objective deleted successfully.');
    }

    public function faqsIndex()
    {
        $faqs = Faqs::orderBy('id', 'asc')->simplePaginate(8);
        return view('pages/settings/faqs', compact('faqs'));
    }

    public function faqsStore(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        Faqs::create([
            'question' => $request->input('question'),
            'answer' => $request->input('answer'),
        ]);

        return redirect()->back()->with('success', 'Faq created successfully.');
    }

    public function faqsUpdate(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:faqs,id',
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        $faq = Faqs::findOrFail($request->id);
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->save();

        return redirect()->back()->with('success', 'FAQ updated successfully.');
    }

    public function faqsDestroy($id)
    {
        $faq = Faqs::findOrFail($id);
        $faq->delete();

        return redirect()->back()->with('success', 'FAQ deleted successfully.');
    }

    public function slidersIndex()
    {
        $sliders = Slider::orderBy('id', 'asc')->get();
        $sliders = Slider::simplePaginate(5);
        return view('pages/settings/sliders', compact('sliders'));
    }

    public function sliderStore(Request $request)
    {
        $request->validate([
            'slider_title' => 'required|string|max:255',
            'slider_text' => 'required|string|max:255',
            'button_name' => 'nullable|string|max:20',
            'button_url' => 'nullable|string|max:2048', // Ensure valid URL format
            'image_url' => 'required|image|mimes:jpg,png,jpeg|max:2048', // Ensure image is uploaded
            'image_url_2' => 'required|image|mimes:jpg,png,jpeg|max:2048', // Ensure image is uploaded
        ],
        [
            'image_url.image' => 'Slider Picture: The uploaded file must be an image.',
            'image_url.mimes' => 'Slider Picture: Only JPG and PNG file types are allowed.',
            'image_url.max' => 'Slider Picture: The image size must not exceed 2 MB.',
            'image_url_2.image' => 'Slider Picture 2: The uploaded file must be an image.',
            'image_url_2.mimes' => 'Slider Picture 2: Only JPG and PNG file types are allowed.',
            'image_url_2.max' => 'Slider Picture 2: The image size must not exceed 2 MB.',
        ]);

        // Handle image_url upload
        if ($request->hasFile('image_url')) {
            $filePath = $request->file('image_url')->store('sliders', 'public');
        }

        // Handle image_url upload
        if ($request->hasFile('image_url_2')) {
            $filePath_2 = $request->file('image_url_2')->store('sliders', 'public');
        }

        Slider::create([
            'title' => $request->input('slider_title'),
            'text' => $request->input('slider_text'),
            'button_name' => $request->input('button_name'),
            'button_url' => $request->input('button_url'),
            'image_url' => $filePath,
            'image_url_2' => $filePath_2,
        ]);

        return redirect()->back()->with('success', 'Slider created successfully.');
    }

    public function sliderUpdate(Request $request, Slider $slider)
    {
        $request->validate([
            'id' => 'required|exists:sliders,id',
            'slider_title' => 'required|string|max:255',
            'slider_text' => 'required|string|max:255',
            'button_name' => 'nullable|string|max:20',
            'button_url' => 'nullable|string|max:2048', // Ensure valid URL format
            'image_url' => 'required|image|mimes:jpg,png,jpeg|max:2048', // Ensure image is uploaded
            'image_url_2' => 'required|image|mimes:jpg,png,jpeg|max:2048', // Ensure image is uploaded
        ],
        [
            'image_url.image' => 'Slider Picture: The uploaded file must be an image.',
            'image_url.mimes' => 'Slider Picture: Only JPG and PNG file types are allowed.',
            'image_url.max' => 'Slider Picture: The image size must not exceed 2 MB.',
            'image_url_2.image' => 'Slider Picture 2: The uploaded file must be an image.',
            'image_url_2.mimes' => 'Slider Picture 2: Only JPG and PNG file types are allowed.',
            'image_url_2.max' => 'Slider Picture 2: The image size must not exceed 2 MB.',
        ]);

        $slider = Slider::findOrFail($request->input('id'));

        // Update fields
        $slider->title = $request->input('slider_title');
        $slider->text = $request->input('slider_text');
        $slider->button_name = $request->input('button_name');
        $slider->button_url = $request->input('button_url');

        // Handle image upload
        if ($request->hasFile('image_url')) {
            $oldImagePath = $slider->image_url;
            $filePath = $request->file('image_url')->store('sliders', 'public');

            $oldImagePath_2 = $slider->image_url_2;
            $filePath_2 = $request->file('image_url_2')->store('sliders', 'public');

            $slider->image_url = $filePath;
            $slider->image_url_2 = $filePath_2;

            if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }

            if ($oldImagePath_2 && Storage::disk('public')->exists($oldImagePath_2)) {
                Storage::disk('public')->delete($oldImagePath_2);
            }
        }

        $slider->save();

        return redirect()->back()->with('success', 'Slider updated successfully!');
    }

    public function slidersDestroy($id)
    {
        $slider = Slider::findOrFail($id);
        $filePath = $slider->image_url;
        $filePath_2 = $slider->image_url_2;
        $slider->delete();

        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }

        if ($filePath_2 && Storage::disk('public')->exists($filePath_2)) {
            Storage::disk('public')->delete($filePath_2);
        }
        return redirect()->back()->with('success', 'Slider deleted successfully.');
    }

    public function founderIndex()
    {
        $founder = Founder::first();
        return view('pages/settings/founder', compact('founder'));
    }

    public function founderUpdate(Request $request)
    {
        $request->validate([
            'founder_picture' => 'nullable|image|mimes:jpg,png,jpeg|max:2048', // Max file size 2MB
            'secondary_picture' => 'nullable|image|mimes:jpg,png,jpeg|max:2048', // Max file size 2MB
            'signature' => 'nullable|image|mimes:jpg,png,jpeg|max:2048', // Max file size 2MB
            'founder_name' => 'required|string|max:255',
            'speech_title' => 'required|string|max:255',
            'speech_content' => 'required|string',
            'facebook_handle' => 'nullable|string',
            'twitter_handle' => 'nullable|string',
            'linkedin_handle' => 'nullable|string',
            'instagram_handle' => 'nullable|string',
            'show_founder' => 'sometimes|boolean'
        ]);

        // Retrieve the founder record or create a new one if not found
        $founder = Founder::first();
        if (!$founder) {
            $founder = new Founder();
        }

        // Handle founder_picture upload
        if ($request->hasFile('founder_picture')) {
            $old_founder_picture = $founder->founder_picture ?? null;
            $founder_picture_filePath = $request->file('founder_picture')->store('founder', 'public');
            $founder->founder_picture = $founder_picture_filePath;

            if ($old_founder_picture && Storage::disk('public')->exists($old_founder_picture)) {
                Storage::disk('public')->delete($old_founder_picture);
            }
        }

        // Handle secondary_picture upload
        if ($request->hasFile('secondary_picture')) {
            $old_secondary_picture = $founder->secondary_picture ?? null;
            $secondary_picture_filePath = $request->file('secondary_picture')->store('founder', 'public');
            $founder->secondary_picture = $secondary_picture_filePath;

            if ($old_secondary_picture && Storage::disk('public')->exists($old_secondary_picture)) {
                Storage::disk('public')->delete($old_secondary_picture);
            }
        }

        // Handle signature upload
        if ($request->hasFile('signature')) {
            $old_signature = $founder->signature ?? null;
            $signature_filePath = $request->file('signature')->store('founder', 'public');
            $founder->signature = $signature_filePath;

            if ($old_signature && Storage::disk('public')->exists($old_signature)) {
                Storage::disk('public')->delete($old_signature);
            }
        }

        // Update other fields
        $founder->founder_name = $request->input('founder_name');
        $founder->speech_title = $request->input('speech_title');
        $founder->speech_content = $request->input('speech_content');
        $founder->facebook_handle = $request->input('facebook_handle');
        $founder->twitter_handle = $request->input('twitter_handle');
        $founder->linkedin_handle = $request->input('linkedin_handle');
        $founder->instagram_handle = $request->input('instagram_handle');
        $founder->show_founder = $request->input('show_founder', false); // Defaults to false if not provided

        $founder->save();

        return redirect()->back()->with('success', 'Founder speech updated successfully!');
    }

    public function customServicesIndex()
    {
        $services = Services::orderBy('id', 'asc')->get();
        $services = Services::simplePaginate(8);
        return view('pages/settings/services', compact('services'));
    }

    public function customServicesStore(Request $request)
    {
        $request->validate([
            'service_name' => 'required|string|max:255',
            'service_description' => 'required|string',
            'service_picture' => 'required|image|mimes:jpg,png,jpeg|max:2048', // Max file size 2MB
        ]);

        // Handle service_picture upload
        if ($request->hasFile('service_picture')) {
            $filePath = $request->file('service_picture')->store('custom-services', 'public');
        }

        Services::create([
            'service_name' => $request->input('service_name'),
            'service_description' => $request->input('service_description'),
            'service_picture' => $filePath,
        ]);

        return redirect()->back()->with('success', 'Custom service created successfully.');
    }

    public function customServicesUpdate(Request $request)
    {
        $request->validate(
            [
                'id' => 'required|exists:services,id',
                'service_name' => 'required|string|max:255',
                'service_description' => 'required|string',
                'service_picture' => 'nullable|image|mimes:jpg,png,jpeg|max:2048', // Optional
            ],
            [
                'service_picture.image' => 'The uploaded file must be an image.',
                'service_picture.mimes' => 'Only JPG and PNG file types are allowed.',
                'service_picture.max' => 'The image size must not exceed 2 MB.',
            ]
        );

        $service = Services::findOrFail($request->input('id'));

        // Update fields
        $service->service_name = $request->input('service_name');
        $service->service_description = $request->input('service_description');

        // Handle image upload
        if ($request->hasFile('service_picture')) {
            $oldImagePath = $service->service_picture;
            $filePath = $request->file('service_picture')->store('custom-services', 'public');

            $service->service_picture = $filePath;

            if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }
        }

        $service->save();

        return redirect()->back()->with('success', 'Custom service updated successfully!');
    }

    public function customServicesDestroy($id)
    {
        $service = Services::findOrFail($id);
        $filePath = $service->service_picture;
        $service->delete();

        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
        return redirect()->back()->with('success', 'Service deleted successfully.');
    }

    public function clientsIndex()
    {
        $clients = Clients::where('type', 'client')->orderBy('name', 'asc')->simplePaginate(8);
        $partners = Clients::where('type', 'partner')->orderBy('name', 'asc')->simplePaginate(8);

        return view('pages/settings/clients', compact('clients', 'partners'));
    }

    public function clientsStore(Request $request)
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
            'type' => 'required|string|in:client,partner',
            'client_logo' => 'required_if:type,client|nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $filePath = null;

        if ($request->hasFile('client_logo')) {
            $filePath = $request->file('client_logo')->store('clients', 'public');
        }

        Clients::create([
            'name' => $request->input('client_name'),
            'type' => $request->input('type'),
            'logo' => $filePath, // Will be null if no file is uploaded
        ]);


        return redirect()->back()->with('success', 'Custom service created successfully.');
    }

    public function clientsDestroy($id)
    {
        $client = Clients::findOrFail($id);
        $filePath = $client->logo;
        $client->delete();

        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
        return redirect()->back()->with('success', 'Partner removed.');
    }

    public function achievementsDestroy($id)
    {
        $achievement = Achievements::findOrFail($id);
        $picture = $achievement->picture;
        $achievement->delete();

        if ($picture && Storage::disk('public')->exists($picture)) {
            Storage::disk('public')->delete($picture);
        }

        return redirect()->back()->with('success', 'Achievement deleted.');
    }

    public function achievementsIndex()
    {
        $achievements = Achievements::orderBy('year', 'desc')->simplePaginate(5);

        return view('pages/settings/achievements', compact('achievements'));
    }

    public function achievementsStore(Request $request)
    {
        // Validate the request data
        $request->validate([
            'year' => 'required|numeric|min:1900|max:' . date('Y'),
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'picture' => 'required|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $filePath = null;

        // Handle banner picture upload
        if ($request->hasFile('picture')) {
            $filePath = $request->file('picture')->store('achievements', 'public');
        }

        // Update other fields
        Achievements::create([
            'year' => $request->input('year'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'picture' => $filePath,
        ]);

        return redirect()->back()->with('success', 'Achievement created successfully!');
    }

    public function achievementsUpdate(Request $request)
    {
        // Validate the request data
        $request->validate([
            'id' => 'required|exists:achievements,id',
            'year' => 'required|numeric|min:1900|max:' . date('Y'),
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'picture' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $achievement = Achievements::findOrFail($request->id);

        // Handle banner picture upload
        if ($request->hasFile('picture')) {
            // Delete old picture if exists
            if ($achievement->picture && Storage::disk('public')->exists($achievement->picture)) {
                Storage::disk('public')->delete($achievement->picture);
            }
            $achievement->picture = $request->file('picture')->store('achievements', 'public');
        }

        // Update other fields
        $achievement->update([
            'year' => $request->input('year'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
        ]);

        return redirect()->back()->with('success', 'Achievement updated successfully!');
    }
}
