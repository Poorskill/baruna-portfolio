<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CvController extends Controller
{
    public function __invoke(): StreamedResponse|Response|RedirectResponse
    {
        $cvPath = SiteSetting::get('cv_path');

        if (! $cvPath || ! Storage::disk('public')->exists($cvPath)) {
            abort(404);
        }

        return Storage::disk('public')->download($cvPath, 'Baruna-Dwi-Cahya-CV.pdf');
    }
}
