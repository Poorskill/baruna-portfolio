<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminGuardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_admin_dashboard(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    public function test_guest_is_redirected_to_login_when_accessing_admin_projects(): void
    {
        $response = $this->get('/admin/projects');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_non_admin_gets_forbidden_status_on_admin_dashboard(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(403);
    }

    public function test_authenticated_non_admin_cannot_mutate_settings(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->put('/admin/settings', [
            'site_name' => 'Hacked Site',
        ]);

        $response->assertStatus(403);
    }

    public function test_authenticated_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
    }

    public function test_authenticated_admin_can_access_admin_projects(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin/projects');

        $response->assertStatus(200);
    }

    public function test_authenticated_admin_can_access_settings(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin/settings');

        $response->assertStatus(200);
    }
}
