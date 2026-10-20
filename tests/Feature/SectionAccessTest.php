<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SectionAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_on_sectioned_route(): void
    {
        $this->get('/todo')->assertRedirect(route('login'));
    }

    public function test_member_without_permission_gets_403(): void
    {
        $user = User::factory()->create(['role' => 'member', 'permissions' => []]);

        $this->actingAs($user)->get('/todo')->assertForbidden();
    }

    public function test_member_with_permission_can_access(): void
    {
        $user = User::factory()->create(['role' => 'member', 'permissions' => ['todo']]);

        $this->actingAs($user)->get('/todo')->assertOk();
    }

    public function test_super_admin_can_access_every_section(): void
    {
        $user = User::factory()->create(['role' => 'super_admin', 'permissions' => []]);

        $this->actingAs($user)->get('/todo')->assertOk();
        $this->actingAs($user)->get('/website-settings')->assertOk();
        $this->actingAs($user)->get('/website-contacts')->assertOk();
        $this->actingAs($user)->get('/join-list')->assertOk();
        $this->actingAs($user)->get('/email-list')->assertOk();
        $this->actingAs($user)->get('/project-list')->assertOk();
    }

    public function test_member_with_website_settings_permission_can_open_settings(): void
    {
        $user = User::factory()->create(['role' => 'member', 'permissions' => ['website_settings']]);

        $this->actingAs($user)->get('/website-settings')->assertOk();
        // Other sections remain closed.
        $this->actingAs($user)->get('/join-list')->assertForbidden();
    }

    public function test_json_request_gets_json_403(): void
    {
        $user = User::factory()->create(['role' => 'member', 'permissions' => []]);

        $this->actingAs($user)
            ->deleteJson('/join-delete/1')
            ->assertForbidden()
            ->assertJson(['message' => 'Forbidden']);
    }

    public function test_project_routes_require_projects_section(): void
    {
        $user = User::factory()->create(['role' => 'member', 'permissions' => ['todo']]);

        $this->actingAs($user)->get('/add-project')->assertForbidden();
        $this->actingAs($user)->get('/project-list')->assertForbidden();
    }
}
