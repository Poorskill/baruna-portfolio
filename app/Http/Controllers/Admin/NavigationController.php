<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavigationLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NavigationController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Navigation/Index', [
            'links' => NavigationLink::orderBy('placement')->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'placement' => 'required|in:header,footer',
            'label' => 'required|string|max:100',
            'url' => ['required', 'string', 'max:500', 'regex:/^(\/|https?:\/\/)/'],
            'is_external' => 'boolean',
            'sort_order' => 'integer|min:0',
            'is_visible' => 'boolean',
        ]);

        NavigationLink::create($data);

        return back()->with('success', 'Navigation link created.');
    }

    public function update(Request $request, NavigationLink $navigation): RedirectResponse
    {
        $data = $request->validate([
            'placement' => 'required|in:header,footer',
            'label' => 'required|string|max:100',
            'url' => ['required', 'string', 'max:500', 'regex:/^(\/|https?:\/\/)/'],
            'is_external' => 'boolean',
            'sort_order' => 'integer|min:0',
            'is_visible' => 'boolean',
        ]);

        $navigation->update($data);

        return back()->with('success', 'Navigation link updated.');
    }

    public function destroy(NavigationLink $navigation): RedirectResponse
    {
        $navigation->delete();

        return back()->with('success', 'Navigation link deleted.');
    }
}
