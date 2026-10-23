<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileContentController extends Controller
{
    public function edit(): Response
    {
        $settings = SiteSetting::where('group', 'about')
            ->orWhere(fn ($q) => $q->whereIn('key', ['site_name', 'site_descriptor', 'cv_path', 'photo_path']))
            ->pluck('value', 'key')
            ->toArray();

        return Inertia::render('Admin/Content/ProfileEdit', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'about_short' => 'nullable|string|max:2000',
            'about_long' => 'nullable|string|max:5000',
            'cv' => 'nullable|file|mimes:pdf|max:10240',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'remove_photo' => 'nullable|boolean',
        ]);

        SiteSetting::set('about_short', $data['about_short'] ?? '', 'about');
        SiteSetting::set('about_long', $data['about_long'] ?? '', 'about');

        if ($request->boolean('remove_photo')) {
            $oldPhoto = SiteSetting::get('photo_path');
            if ($oldPhoto) {
                Storage::disk('public')->delete($oldPhoto);
            }
            SiteSetting::set('photo_path', '', 'identity');
        } elseif ($request->hasFile('photo')) {
            $oldPhoto = SiteSetting::get('photo_path');
            if ($oldPhoto) {
                Storage::disk('public')->delete($oldPhoto);
            }
            $path = $request->file('photo')->store('profile/photos', 'public');
            SiteSetting::set('photo_path', $path, 'identity');
        }

        if ($request->hasFile('cv')) {
            $oldCv = SiteSetting::get('cv_path');
            if ($oldCv) {
                Storage::disk('public')->delete($oldCv);
            }
            $path = $request->file('cv')->store('documents', 'public');
            SiteSetting::set('cv_path', $path, 'identity');
        }

        ActivityLog::record('updated', 'SiteSetting', null, 'Profile content and photo updated');

        return back()->with('success', 'Profile content saved.');
    }
}
