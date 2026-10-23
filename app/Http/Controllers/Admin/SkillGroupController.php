<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SkillGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class SkillGroupController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/SkillGroups/Index', [
            'groups' => SkillGroup::withCount('skills')->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|regex:/^[a-z0-9\-]+$/|unique:skill_groups,slug',
            'description' => 'nullable|string|max:500',
            'sort_order' => 'integer|min:0',
            'is_visible' => 'boolean',
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        SkillGroup::create($data);
        ActivityLog::record('created', 'SkillGroup', null, "Created skill group: {$data['name']}");

        return back()->with('success', 'Skill group created.');
    }

    public function update(Request $request, SkillGroup $skillGroup): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9\-]+$/', Rule::unique('skill_groups', 'slug')->ignore($skillGroup)],
            'description' => 'nullable|string|max:500',
            'sort_order' => 'integer|min:0',
            'is_visible' => 'boolean',
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $skillGroup->update($data);

        return back()->with('success', 'Skill group updated.');
    }

    public function destroy(SkillGroup $skillGroup): RedirectResponse
    {
        $skillGroup->skills()->update(['skill_group_id' => null]);
        $skillGroup->delete();

        return back()->with('success', 'Skill group deleted.');
    }
}
