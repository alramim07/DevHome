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

    public function test_teams_page_counts_only_active_members(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin', 'status' => 'active']);
        $team = Team::create(['name' => 'Backend']);

        $activeUser1 = User::factory()->create(['name' => 'Alice Active', 'status' => 'active']);
        $activeUser2 = User::factory()->create(['name' => 'Bob Active', 'status' => 'active']);
        $suspendedUser = User::factory()->create(['name' => 'Charlie Suspended', 'status' => 'suspended']);
        $deletedUser = User::factory()->create(['name' => 'David Deleted', 'status' => 'active', 'deleted_at' => now()]);

        $team->users()->attach([$activeUser1->id, $activeUser2->id, $suspendedUser->id, $deletedUser->id]);

        $response = $this->actingAs($admin)->get(route('team.teams'));
        $response->assertOk();

        // The active count should be 2 (Alice and Bob), excluding Charlie (suspended) and David (deleted)
        $teamsData = $response->viewData('teams');
        $backendTeam = $teamsData->firstWhere('id', $team->id);

        $this->assertNotNull($backendTeam);
        $this->assertEquals(2, $backendTeam->active_members_count);

        // Candidates for adding should not include suspended users
        $candidates = $response->viewData('teamCandidates');
        $this->assertFalse($candidates->contains('id', $suspendedUser->id));
        $this->assertTrue($candidates->contains('id', $activeUser1->id));
    }

    public function test_positions_page_counts_only_active_members(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $position = \App\Models\Position::create(['name' => 'QA Engineer', 'permissions' => []]);

        User::factory()->create(['position_id' => $position->id, 'status' => 'active']);
        User::factory()->create(['position_id' => $position->id, 'status' => 'suspended']);

        $response = $this->actingAs($admin)->get(route('team.positions'));
        $response->assertOk();

        $positions = $response->viewData('positions');
        $qaPosition = $positions->firstWhere('id', $position->id);

        $this->assertNotNull($qaPosition);
        $this->assertEquals(1, $qaPosition->users_count);
    }

    public function test_bulk_add_members_to_team(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $team = Team::create(['name' => 'Mobile Dev']);

        $user1 = User::factory()->create(['name' => 'User One']);
        $user2 = User::factory()->create(['name' => 'User Two']);
        $user3 = User::factory()->create(['name' => 'User Three']);

        $res = $this->actingAs($admin)
            ->post(route('team.teams.members.add', $team), [
                'user_ids' => [$user1->id, $user2->id, $user3->id],
            ]);

        $res->assertRedirect();
        $this->assertDatabaseHas('team_user', ['team_id' => $team->id, 'user_id' => $user1->id]);
        $this->assertDatabaseHas('team_user', ['team_id' => $team->id, 'user_id' => $user2->id]);
        $this->assertDatabaseHas('team_user', ['team_id' => $team->id, 'user_id' => $user3->id]);
        $this->assertEquals(3, $team->fresh()->users()->count());
    }

    public function test_bulk_add_members_requires_at_least_one(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $team = Team::create(['name' => 'Mobile Dev']);

        $res = $this->actingAs($admin)
            ->from(route('team.teams'))
            ->post(route('team.teams.members.add', $team), [
                'user_ids' => [],
            ]);

        $res->assertSessionHas('error');
    }

    public function test_ajax_add_and_remove_member(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);
        $team = Team::create(['name' => 'Data Science']);

        $user1 = User::factory()->create(['name' => 'Data Analyst', 'status' => 'active']);
        $user2 = User::factory()->create(['name' => 'Data Engineer', 'status' => 'active']);

        // 1. AJAX Add Members
        $addRes = $this->actingAs($admin)
            ->postJson(route('team.teams.members.add', $team), [
                'user_ids' => [$user1->id, $user2->id],
            ]);

        $addRes->assertOk()->assertJson([
            'success' => true,
            'team_id' => $team->id,
            'active_members_count' => 2,
        ]);
        $this->assertEquals(2, $team->fresh()->users()->count());

        // 2. AJAX Remove Member
        $removeRes = $this->actingAs($admin)
            ->postJson(route('team.teams.members.remove', [$team, $user1->id]));

        $removeRes->assertOk()->assertJson([
            'success' => true,
            'team_id' => $team->id,
            'user_id' => $user1->id,
            'active_members_count' => 1,
        ]);
        $this->assertEquals(1, $team->fresh()->users()->count());
    }
}
