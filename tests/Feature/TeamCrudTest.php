<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_anyone_with_section_can_create_team(): void
    {
        $member = User::factory()->create(['role' => 'member', 'permissions' => ['team_management']]);

        $this->actingAs($member)
            ->post(route('team.teams.store'), ['name' => 'Design Team'])
            ->assertRedirect();

        $this->assertDatabaseHas('teams', ['name' => 'Design Team']);
    }

    public function test_team_name_required_and_unique(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);
        Team::create(['name' => 'Frontend']);

        $this->actingAs($user)
            ->post(route('team.teams.store'), ['name' => ''])
            ->assertSessionHasErrors('name');

        $this->actingAs($user)
            ->post(route('team.teams.store'), ['name' => 'Frontend'])
            ->assertSessionHasErrors('name');
    }

    public function test_super_admin_can_rename_and_delete_team(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);
        $team = Team::create(['name' => 'Old']);

        $this->actingAs($user)
            ->post(route('team.teams.update', $team), ['name' => 'Renamed'])
            ->assertRedirect();
        $this->assertSame('Renamed', $team->fresh()->name);

        $this->actingAs($user)
            ->post(route('team.teams.delete', $team))
            ->assertRedirect();
        $this->assertDatabaseMissing('teams', ['id' => $team->id]);
    }

    public function test_member_with_section_can_manage_teams(): void
    {
        $member = User::factory()->create(['role' => 'member', 'permissions' => ['team_management']]);
        $team = Team::create(['name' => 'Ops']);

        $this->actingAs($member)
            ->post(route('team.teams.update', $team), ['name' => 'Ops Team'])
            ->assertRedirect();
        $this->assertSame('Ops Team', $team->fresh()->name);
    }

    public function test_member_without_section_cannot_manage_teams(): void
    {
        $user = User::factory()->create(['role' => 'member', 'permissions' => []]);
        $team = Team::create(['name' => 'Ops']);

        $this->actingAs($user)
            ->post(route('team.teams.store'), ['name' => 'Nope'])
            ->assertForbidden();
        $this->actingAs($user)
            ->post(route('team.teams.delete', $team))
            ->assertForbidden();
    }

    public function test_member_can_be_added_to_and_removed_from_team(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $member = User::factory()->create(['role' => 'member']);
        $team = Team::create(['name' => 'Design']);

        $this->actingAs($admin)
            ->post(route('team.teams.members.add', $team), ['user_id' => $member->id])
            ->assertRedirect();
        $this->assertDatabaseHas('team_user', ['team_id' => $team->id, 'user_id' => $member->id]);

        // Adding the same member twice is rejected gracefully.
        $this->actingAs($admin)
            ->from(route('team.teams'))
            ->post(route('team.teams.members.add', $team), ['user_id' => $member->id])
            ->assertSessionHas('error');

        $this->actingAs($admin)
            ->post(route('team.teams.members.remove', [$team, $member->id]))
            ->assertRedirect();
        $this->assertDatabaseMissing('team_user', ['team_id' => $team->id, 'user_id' => $member->id]);
    }

    public function test_adding_nonexistent_user_is_rejected(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $team = Team::create(['name' => 'Design']);

        $this->actingAs($admin)
            ->post(route('team.teams.members.add', $team), ['user_id' => 999999])
            ->assertSessionHasErrors('user_id');
    }
}
