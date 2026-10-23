<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Education;
use App\Models\Experience;
use App\Models\NavigationLink;
use App\Models\SiteSetting;
use App\Models\SocialLink;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class AboutController extends Controller
{
    public function __invoke(): Response
    {
        $settings = SiteSetting::whereIn('group', ['identity', 'about'])->pluck('value', 'key')->toArray();

        $education = Education::where('is_visible', true)
            ->orderBy('sort_order')
            ->orderByDesc('start_date')
            ->get();

        $experiences = Experience::where('is_visible', true)
            ->orderBy('sort_order')
            ->orderByDesc('start_date')
            ->get();

        $certificates = Certificate::where('is_visible', true)
            ->orderBy('sort_order')
            ->get();

        $cvPath = $settings['cv_path'] ?? null;
        $hasCv = ! empty($cvPath) && Storage::disk('public')->exists($cvPath);

        return Inertia::render('Public/About', [
            'settings' => $settings,
            'education' => $education,
            'experiences' => $experiences,
            'certificates' => $certificates,
            'navLinks' => NavigationLink::visible()->header()->orderBy('sort_order')->get(['id', 'label', 'url', 'is_external']),
            'socialLinks' => SocialLink::visible()->orderBy('sort_order')->get(['id', 'label', 'platform', 'url']),
            'hasCv' => $hasCv,
        ]);
    }
}
