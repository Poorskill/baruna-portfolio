<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Education;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EducationController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Education/Index', [
            'education' => Education::orderBy('sort_order')->orderByDesc('start_date')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'institution' => 'required|string|max:255',
            'qualification' => 'required|string|max:255',
            'field_of_study' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'expected_end_date' => 'nullable|date',
            'status' => 'required|in:in_progress,completed,dropped',
            'description' => 'nullable|string|max:2000',
            'sort_order' => 'integer|min:0',
            'is_visible' => 'boolean',
        ]);

        Education::create($data);
        ActivityLog::record('created', 'Education', null, "Created education: {$data['institution']}");

        return back()->with('success', 'Education created.');
    }

    public function update(Request $request, Education $education): RedirectResponse
    {
        $data = $request->validate([
            'institution' => 'required|string|max:255',
            'qualification' => 'required|string|max:255',
            'field_of_study' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'expected_end_date' => 'nullable|date',
            'status' => 'required|in:in_progress,completed,dropped',
            'description' => 'nullable|string|max:2000',
            'sort_order' => 'integer|min:0',
            'is_visible' => 'boolean',
        ]);

        $education->update($data);

        return back()->with('success', 'Education updated.');
    }

    public function destroy(Education $education): RedirectResponse
    {
        $education->delete();

        return back()->with('success', 'Education deleted.');
    }
}
