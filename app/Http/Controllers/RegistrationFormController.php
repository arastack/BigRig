<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use App\Models\TrainingObjective;
use Barryvdh\DomPDF\Facade\Pdf;

class RegistrationFormController extends Controller
{
    /**
     * Download a blank, printable copy of the online application form.
     */
    public function download()
    {
        $settings = Settings::first();
        $courses = TrainingObjective::all();

        // dompdf cannot fetch URLs reliably, so embed the logo directly
        $logo = null;
        $logoPath = $settings?->light_theme_logo ? public_path('storage/' . $settings->light_theme_logo) : null;
        if ($logoPath && is_file($logoPath)) {
            $logo = 'data:' . mime_content_type($logoPath) . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('pdf.application-form', compact('settings', 'courses', 'logo'))
            ->setPaper('a4');

        return $pdf->download('BigRig-Application-Form.pdf');
    }
}
