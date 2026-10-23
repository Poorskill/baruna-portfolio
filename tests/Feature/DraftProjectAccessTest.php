<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DraftProjectAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_view_draft_project(): void
    {
        $project = Project::create([
            'title' => 'Secret Draft Project',
            'slug' => 'secret-draft-project',
            'summary' => 'Confidential internal system summary',
            'body' => 'Secret internal architecture details',
            'status' => 'draft',
            'sort_order' => 0,
        ]);

        $response = $this->get('/projects/'.$project->slug);

        $response->assertStatus(404);
        $response->assertDontSee('Confidential internal system summary');
        $response->assertDontSee('Secret internal architecture details');
    }

    public function test_non_admin_user_cannot_view_draft_project(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $project = Project::create([
            'title' => 'Unpublished Project',
            'slug' => 'unpublished-project',
            'summary' => 'Confidential summary',
            'status' => 'draft',
            'sort_order' => 0,
        ]);

        $response = $this->actingAs($user)->get('/projects/'.$project->slug);

        $response->assertStatus(404);
        $response->assertDontSee('Confidential summary');
    }

    public function test_guest_cannot_view_archived_project(): void
    {
        $project = Project::create([
            'title' => 'Archived Project',
            'slug' => 'archived-project',
            'summary' => 'Old archived project summary',
            'status' => 'archived',
            'sort_order' => 0,
        ]);

        $response = $this->get('/projects/'.$project->slug);

        $response->assertStatus(404);
        $response->assertDontSee('Old archived project summary');
    }

    public function test_guest_can_view_published_project(): void
    {
        $project = Project::create([
            'title' => 'Public Showcase Project',
            'slug' => 'public-showcase-project',
            'summary' => 'Open source full-stack system',
            'status' => 'published',
            'published_at' => now(),
            'sort_order' => 0,
        ]);

        $response = $this->get('/projects/'.$project->slug);

        $response->assertStatus(200);
        $response->assertSee('Public Showcase Project');
    }
}
