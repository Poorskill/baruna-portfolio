<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SiteSettingController extends Controller
{
    public function edit(): Response
    {
        $settings = SiteSetting::all()->pluck('value', 'key')->toArray();

        return Inertia::render('Admin/Settings/Edit', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_descriptor' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_whatsapp' => 'nullable|string|max:50',
            'availability_status' => 'nullable|string|max:255',
            'footer_text' => 'nullable|string|max:500',
            'default_meta_title' => 'nullable|string|max:255',
            'default_meta_description' => 'nullable|string|max:500',
        ]);

        foreach ($validated as $key => $value) {
            SiteSetting::set($key, $value ?? '', $this->groupForKey($key));
        }

        ActivityLog::record('updated', 'SiteSetting', null, 'Site settings updated');

        return back()->with('success', 'Settings saved.');
    }

    private function groupForKey(string $key): string
    {
        return match (true) {
            str_starts_with($key, 'default_meta') => 'seo',
            in_array($key, ['contact_email', 'contact_whatsapp']) => 'contact',
            in_array($key, ['footer_text']) => 'footer',
            default => 'identity',
        };
    }
}
