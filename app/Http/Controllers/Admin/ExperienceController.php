<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Experience;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExperienceController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Experiences/Index', [
            'experiences' => Experience::orderBy('sort_order')->orderByDesc('start_date')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'organization' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'location_mode' => 'nullable|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'boolean',
            'summary' => 'nullable|string|max:2000',
            'responsibilities' => 'nullable|array',
            'responsibilities.*' => 'string|max:500',
            'outcomes' => 'nullable|array',
            'outcomes.*' => 'string|max:500',
            'sort_order' => 'integer|min:0',
            'is_visible' => 'boolean',
        ]);

        Experience::create($data);
        ActivityLog::record('created', 'Experience', null, "Created experience: {$data['organization']}");

        return back()->with('success', 'Experience created.');
    }

    public function update(Request $request, Experience $experience): RedirectResponse
    {
        $data = $request->validate([
            'organization' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'location_mode' => 'nullable|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'boolean',
            'summary' => 'nullable|string|max:2000',
            'responsibilities' => 'nullable|array',
            'responsibilities.*' => 'string|max:500',
            'outcomes' => 'nullable|array',
            'outcomes.*' => 'string|max:500',
            'sort_order' => 'integer|min:0',
            'is_visible' => 'boolean',
        ]);

        $experience->update($data);

        return back()->with('success', 'Experience updated.');
    }

    public function destroy(Experience $experience): RedirectResponse
    {
        $experience->delete();

        return back()->with('success', 'Experience deleted.');
    }
}
