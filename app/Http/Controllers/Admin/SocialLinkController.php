<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SocialLinkController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/SocialLinks/Index', [
            'links' => SocialLink::orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'label' => 'required|string|max:100',
            'platform' => 'required|string|max:100',
            'url' => ['required', 'string', 'max:500', 'regex:/^(https?:\/\/|mailto:)/'],
            'sort_order' => 'integer|min:0',
            'is_visible' => 'boolean',
        ]);

        SocialLink::create($data);

        return back()->with('success', 'Social link created.');
    }

    public function update(Request $request, SocialLink $socialLink): RedirectResponse
    {
        $data = $request->validate([
            'label' => 'required|string|max:100',
            'platform' => 'required|string|max:100',
            'url' => ['required', 'string', 'max:500', 'regex:/^(https?:\/\/|mailto:)/'],
            'sort_order' => 'integer|min:0',
            'is_visible' => 'boolean',
        ]);

        $socialLink->update($data);

        return back()->with('success', 'Social link updated.');
    }

    public function destroy(SocialLink $socialLink): RedirectResponse
    {
        $socialLink->delete();

        return back()->with('success', 'Social link deleted.');
    }
}
