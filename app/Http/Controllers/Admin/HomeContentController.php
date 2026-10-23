<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeContentController extends Controller
{
    public function edit(): Response
    {
        $settings = SiteSetting::where('group', 'home')->pluck('value', 'key')->toArray();

        return Inertia::render('Admin/Content/HomeEdit', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hero_headline' => 'required|string|max:500',
            'hero_description' => 'required|string|max:1000',
            'hero_cta_primary_label' => 'nullable|string|max:100',
            'hero_cta_primary_url' => 'nullable|string|max:255',
            'hero_cta_secondary_label' => 'nullable|string|max:100',
            'hero_cta_secondary_url' => 'nullable|string|max:255',
        ]);

        foreach ($validated as $key => $value) {
            SiteSetting::set($key, $value ?? '', 'home');
        }

        ActivityLog::record('updated', 'SiteSetting', null, 'Home content updated');

        return back()->with('success', 'Home content saved.');
    }
}
