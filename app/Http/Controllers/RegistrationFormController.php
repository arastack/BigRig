<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use App\Models\TrainingObjective;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RegistrationFormController extends Controller
{
    /**
     * Download a blank, printable copy of the online application form.
     */
    public function download()
    {
        $settings = Settings::first();
        $courses = TrainingObjective::all();
        $logo = $this->embeddedLogo($settings?->light_theme_logo);

        try {
            $pdf = Pdf::loadView('pdf.application-form', compact('settings', 'courses', 'logo'))
                ->setPaper('a4');

            return $pdf->download('BigRig-Application-Form.pdf');
        } catch (\Throwable $e) {
            if ($logo === null) {
                throw $e;
            }

            // Image rendering depends on server extensions (e.g. GD); retry without the logo
            Log::warning('Application form PDF failed with logo, retrying without it: ' . $e->getMessage());

            $logo = null;

            return Pdf::loadView('pdf.application-form', compact('settings', 'courses', 'logo'))
                ->setPaper('a4')
                ->download('BigRig-Application-Form.pdf');
        }
    }

    /**
     * dompdf cannot fetch URLs reliably, so embed the logo as a data URI.
     */
    private function embeddedLogo(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        // public/storage may be a real folder or a symlink to storage/app/public depending on the host
        $candidates = [public_path('storage/' . $path), Storage::disk('public')->path($path)];
        $file = collect($candidates)->first(fn ($candidate) => is_file($candidate));

        $mimeTypes = ['png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif'];
        $mime = $mimeTypes[strtolower(pathinfo($path, PATHINFO_EXTENSION))] ?? null;

        if (! $file || ! $mime) {
            return null;
        }

        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($file));
    }
}
