<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Skill;
use App\Models\SkillGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class SkillController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Skills/Index', [
            'skills' => Skill::with('group:id,name')->orderBy('sort_order')->get(),
            'groups' => SkillGroup::orderBy('sort_order')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|regex:/^[a-z0-9\-]+$/|unique:skills,slug',
            'skill_group_id' => 'nullable|exists:skill_groups,id',
            'note' => 'nullable|string|max:500',
            'proficiency' => 'nullable|string|max:100',
            'sort_order' => 'integer|min:0',
            'is_visible' => 'boolean',
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        Skill::create($data);
        ActivityLog::record('created', 'Skill', null, "Created skill: {$data['name']}");

        return back()->with('success', 'Skill created.');
    }

    public function update(Request $request, Skill $skill): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9\-]+$/', Rule::unique('skills', 'slug')->ignore($skill)],
            'skill_group_id' => 'nullable|exists:skill_groups,id',
            'note' => 'nullable|string|max:500',
            'proficiency' => 'nullable|string|max:100',
            'sort_order' => 'integer|min:0',
            'is_visible' => 'boolean',
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $skill->update($data);

        return back()->with('success', 'Skill updated.');
    }

    public function destroy(Skill $skill): RedirectResponse
    {
        $skill->projects()->detach();
        $skill->delete();

        return back()->with('success', 'Skill deleted.');
    }
}
