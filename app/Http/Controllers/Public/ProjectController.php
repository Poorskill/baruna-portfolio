<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\NavigationLink;
use App\Models\Project;
use App\Models\SiteSetting;
use App\Models\SocialLink;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Project::published()
            ->with('skills:id,name,slug')
            ->when($request->category, fn ($q, $c) => $q->where('category', $c))
            ->when($request->search, fn ($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                    ->orWhere('summary', 'like', "%{$s}%");
            }));

        $projects = $query
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->paginate(12)
            ->withQueryString();

        $categories = Project::published()
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        return Inertia::render('Public/Projects/Index', [
            'projects' => $projects,
            'categories' => $categories,
            'filters' => $request->only('search', 'category'),
            'navLinks' => NavigationLink::visible()->header()->orderBy('sort_order')->get(['id', 'label', 'url', 'is_external']),
            'socialLinks' => SocialLink::visible()->orderBy('sort_order')->get(['id', 'label', 'platform', 'url']),
            'siteName' => SiteSetting::get('site_name', 'Baruna Dwi Cahya'),
        ]);
    }

    public function show(Project $project): Response
    {
        if ($project->status !== 'published') {
            abort(404);
        }

        $project->load('skills:id,name,slug', 'images');

        $settings = ['site_name' => SiteSetting::get('site_name', 'Baruna Dwi Cahya')];

        return Inertia::render('Public/Projects/Show', [
            'project' => $project,
            'settings' => $settings,
            'navLinks' => NavigationLink::visible()->header()->orderBy('sort_order')->get(['id', 'label', 'url', 'is_external']),
            'socialLinks' => SocialLink::visible()->orderBy('sort_order')->get(['id', 'label', 'platform', 'url']),
        ]);
    }
}
