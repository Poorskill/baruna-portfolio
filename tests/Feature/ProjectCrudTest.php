<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_valid_project(): void
    {
        $admin = User::factory()->admin()->create();
        $skill = Skill::create([
            'name' => 'Laravel',
            'slug' => 'laravel',
            'sort_order' => 1,
            'is_visible' => true,
        ]);

        $response = $this->actingAs($admin)->post('/admin/projects', [
            'title' => 'Enterprise Billing Platform',
            'slug' => 'enterprise-billing-platform',
            'summary' => 'High throughput invoice reconciliation platform',
            'body' => 'Detailed system architecture overview',
            'category' => 'Fintech',
            'role' => 'Principal Backend Engineer',
            'problem' => 'Manual reconciliation took days and suffered from concurrency locks',
            'solution' => 'Implemented transactional batch queues and idempotency keys',
            'features' => 'Multi-currency ledger, async job workers, PDF statement generator',
            'status' => 'published',
            'sort_order' => 1,
            'is_featured' => true,
            'demo_url' => 'https://demo.example.com',
            'source_url' => 'https://github.com/example/billing',
            'skill_ids' => [$skill->id],
        ]);

        $response->assertRedirect(route('admin.projects.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('projects', [
            'title' => 'Enterprise Billing Platform',
            'slug' => 'enterprise-billing-platform',
            'status' => 'published',
            'is_featured' => true,
        ]);

        $project = Project::where('slug', 'enterprise-billing-platform')->first();
        $this->assertNotNull($project);
        $this->assertTrue($project->skills->contains($skill->id));

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'created',
            'entity_type' => 'Project',
            'entity_id' => $project->id,
        ]);
    }

    public function test_title_and_status_are_required_to_create_project(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/projects', [
            'title' => '',
            'status' => '',
        ]);

        $response->assertSessionHasErrors(['title', 'status']);
        $this->assertDatabaseCount('projects', 0);
    }

    public function test_duplicate_slug_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();

        Project::create([
            'title' => 'First Project',
            'slug' => 'unique-slug',
            'status' => 'draft',
            'sort_order' => 0,
        ]);

        $response = $this->actingAs($admin)->post('/admin/projects', [
            'title' => 'Second Project',
            'slug' => 'unique-slug',
            'status' => 'draft',
            'sort_order' => 1,
        ]);

        $response->assertSessionHasErrors('slug');
    }

    public function test_invalid_demo_url_scheme_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/projects', [
            'title' => 'XSS Link Project',
            'status' => 'draft',
            'demo_url' => 'javascript:alert(1)',
        ]);

        $response->assertSessionHasErrors('demo_url');
    }

    public function test_admin_can_update_project(): void
    {
        $admin = User::factory()->admin()->create();

        $project = Project::create([
            'title' => 'Initial Title',
            'slug' => 'initial-title',
            'summary' => 'Initial summary',
            'status' => 'draft',
            'sort_order' => 0,
        ]);

        $response = $this->actingAs($admin)->put('/admin/projects/'.$project->id, [
            'title' => 'Updated Title',
            'slug' => 'updated-title',
            'summary' => 'Updated summary description',
            'status' => 'published',
            'sort_order' => 5,
        ]);

        $response->assertRedirect(route('admin.projects.index'));

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'title' => 'Updated Title',
            'slug' => 'updated-title',
            'status' => 'published',
            'sort_order' => 5,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'updated',
            'entity_type' => 'Project',
            'entity_id' => $project->id,
        ]);
    }

    public function test_admin_can_delete_project(): void
    {
        $admin = User::factory()->admin()->create();

        $project = Project::create([
            'title' => 'Project to Delete',
            'slug' => 'project-to-delete',
            'status' => 'draft',
            'sort_order' => 0,
        ]);

        $response = $this->actingAs($admin)->delete('/admin/projects/'.$project->id);

        $response->assertRedirect(route('admin.projects.index'));
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'deleted',
            'entity_type' => 'Project',
            'entity_id' => $project->id,
        ]);
    }

    public function test_non_admin_cannot_mutate_projects(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $project = Project::create([
            'title' => 'Protected Project',
            'slug' => 'protected-project',
            'status' => 'draft',
            'sort_order' => 0,
        ]);

        $this->actingAs($user)->post('/admin/projects', [
            'title' => 'Hacker Project',
            'status' => 'published',
        ])->assertStatus(403);

        $this->actingAs($user)->put('/admin/projects/'.$project->id, [
            'title' => 'Hacked Title',
            'status' => 'published',
        ])->assertStatus(403);

        $this->actingAs($user)->delete('/admin/projects/'.$project->id)
            ->assertStatus(403);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'title' => 'Protected Project',
        ]);
    }
}
