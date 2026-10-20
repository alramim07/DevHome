<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamManagementAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_without_team_management_gets_403(): void
    {
        $user = User::factory()->create(['role' => 'member', 'permissions' => ['todo']]);

        $this->actingAs($user)->get('/positions')->assertForbidden();
        $this->actingAs($user)->get('/teams')->assertForbidden();
    }

    public function test_member_with_team_management_can_view(): void
    {
        $user = User::factory()->create(['role' => 'member', 'permissions' => ['team_management']]);

        $this->actingAs($user)->get('/positions')->assertOk();
        $this->actingAs($user)->get('/teams')->assertOk();
    }

    public function test_super_admin_can_view(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($user)->get('/positions')->assertOk();
        $this->actingAs($user)->get('/teams')->assertOk();
    }

    public function test_members_route_redirects_until_task5(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($user)->get('/members')->assertRedirect(route('team.positions'));
    }
}
