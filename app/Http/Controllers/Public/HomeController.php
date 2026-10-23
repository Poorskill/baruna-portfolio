<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\NavigationLink;
use App\Models\Project;
use App\Models\SiteSetting;
use App\Models\SkillGroup;
use App\Models\SocialLink;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        $settings = SiteSetting::all()->pluck('value', 'key')->toArray();

        $featuredProjects = Project::published()
            ->featured()
            ->with('skills:id,name,slug')
            ->orderBy('sort_order')
            ->take(6)
            ->get([
                'id', 'title', 'slug', 'summary', 'category',
                'cover_image', 'demo_url', 'source_url', 'status', 'is_featured',
            ]);

        $skillGroups = SkillGroup::where('is_visible', true)
            ->with(['visibleSkills'])
            ->orderBy('sort_order')
            ->get(['id', 'name', 'sort_order']);

        $cvPath = $settings['cv_path'] ?? null;
        $hasCv = ! empty($cvPath) && Storage::disk('public')->exists($cvPath);

        return Inertia::render('Public/Home', [
            'settings' => $settings,
            'featuredProjects' => $featuredProjects,
            'skillGroups' => $skillGroups,
            'navLinks' => NavigationLink::visible()->header()->orderBy('sort_order')->get(['id', 'label', 'url', 'is_external']),
            'socialLinks' => SocialLink::visible()->orderBy('sort_order')->get(['id', 'label', 'platform', 'url']),
            'hasCv' => $hasCv,
        ]);
    }
}
