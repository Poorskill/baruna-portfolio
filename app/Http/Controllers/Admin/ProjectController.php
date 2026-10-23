<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\Skill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(Request $request): Response
    {
        $projects = Project::query()
            ->when($request->search, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Projects/Index', [
            'projects' => $projects,
            'filters' => $request->only('search', 'status'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Projects/Form', [
            'project' => null,
            'skills' => Skill::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        return DB::transaction(function () use ($request, $data) {
            $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
            $data['published_at'] = $data['status'] === 'published' ? now() : null;

            if ($request->hasFile('cover_image')) {
                $data['cover_image'] = $request->file('cover_image')->store('projects/covers', 'public');
            }

            $project = Project::create($data);

            if (! empty($data['skill_ids'])) {
                $project->skills()->sync($data['skill_ids']);
            }

            $this->handleGalleryUpload($request, $project);

            ActivityLog::record('created', 'Project', $project->id, "Created project: {$project->title}");

            return redirect()->route('admin.projects.index')->with('success', 'Project created.');
        });
    }

    public function edit(Project $project): Response
    {
        $project->load('skills:id,name', 'images');

        return Inertia::render('Admin/Projects/Form', [
            'project' => $project,
            'skills' => Skill::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $data = $this->validated($request, $project);

        return DB::transaction(function () use ($request, $data, $project) {
            if (! $project->published_at && $data['status'] === 'published') {
                $data['published_at'] = now();
            }

            if ($request->hasFile('cover_image')) {
                if ($project->cover_image) {
                    Storage::disk('public')->delete($project->cover_image);
                }
                $data['cover_image'] = $request->file('cover_image')->store('projects/covers', 'public');
            }

            $project->update($data);
            $project->skills()->sync($data['skill_ids'] ?? []);

            $this->handleGalleryUpload($request, $project);

            ActivityLog::record('updated', 'Project', $project->id, "Updated project: {$project->title}");

            return redirect()->route('admin.projects.index')->with('success', 'Project updated.');
        });
    }

    public function destroy(Project $project): RedirectResponse
    {
        DB::transaction(function () use ($project) {
            foreach ($project->images as $image) {
                Storage::disk('public')->delete($image->path);
            }
            if ($project->cover_image) {
                Storage::disk('public')->delete($project->cover_image);
            }
            $project->skills()->detach();
            $project->images()->delete();
            $project->delete();

            ActivityLog::record('deleted', 'Project', $project->id, "Deleted project: {$project->title}");
        });

        return redirect()->route('admin.projects.index')->with('success', 'Project deleted.');
    }

    public function destroyImage(Project $project, ProjectImage $image): RedirectResponse
    {
        if ($image->project_id !== $project->id) {
            abort(403);
        }
        Storage::disk('public')->delete($image->path);
        $image->delete();

        return back()->with('success', 'Image removed.');
    }

    private function validated(Request $request, ?Project $project = null): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'slug' => [
                'nullable', 'string', 'max:255', 'regex:/^[a-z0-9\-]+$/',
                Rule::unique('projects', 'slug')->ignore($project),
            ],
            'summary' => 'nullable|string|max:1000',
            'body' => 'nullable|string|max:10000',
            'category' => 'nullable|string|max:100',
            'role' => 'nullable|string|max:255',
            'problem' => 'nullable|string|max:5000',
            'solution' => 'nullable|string|max:5000',
            'features' => 'nullable|string|max:5000',
            'challenges' => 'nullable|string|max:5000',
            'outcome_or_learning' => 'nullable|string|max:5000',
            'demo_url' => 'nullable|url:https,http|max:500',
            'source_url' => 'nullable|url:https,http|max:500',
            'documentation_url' => 'nullable|url:https,http|max:500',
            'cover_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_featured' => 'boolean',
            'status' => 'required|in:draft,published,archived',
            'sort_order' => 'integer|min:0',
            'skill_ids' => 'nullable|array',
            'skill_ids.*' => 'exists:skills,id',
        ];

        $data = $request->validate($rules);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        return $data;
    }

    private function handleGalleryUpload(Request $request, Project $project): void
    {
        if ($request->hasFile('gallery')) {
            $request->validate([
                'gallery.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120',
            ]);

            $maxOrder = $project->images()->max('sort_order') ?? 0;
            foreach ($request->file('gallery') as $file) {
                $path = $file->store('projects/gallery', 'public');
                $project->images()->create([
                    'path' => $path,
                    'sort_order' => ++$maxOrder,
                ]);
            }
        }
    }
}
