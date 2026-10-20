<?php

namespace Tests\Feature;

use App\Models\Position;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_positions_table_has_name_and_permissions(): void
    {
        $position = Position::create([
            'name' => 'Developer',
            'permissions' => ['projects', 'todo'],
        ]);

        $this->assertDatabaseHas('positions', ['name' => 'Developer']);
        $this->assertSame(['projects', 'todo'], $position->fresh()->permissions);
    }

    public function test_teams_table_has_name_and_users_pivot(): void
    {
        $user = User::factory()->create();
        $team = Team::create(['name' => 'Design Team']);
        $team->users()->attach($user->id);

        $this->assertDatabaseHas('team_user', [
            'team_id' => $team->id,
            'user_id' => $user->id,
        ]);
        $this->assertTrue($user->fresh()->teams->contains('id', $team->id));
        $this->assertTrue($team->fresh()->users->contains('id', $user->id));
    }

    public function test_users_have_role_position_and_permissions_columns(): void
    {
        $position = Position::create(['name' => 'Designer', 'permissions' => []]);
        $user = User::factory()->create();

        $user->update([
            'role' => 'admin',
            'position_id' => $position->id,
            'permissions' => ['projects'],
        ]);

        $fresh = $user->fresh();
        $this->assertSame('admin', $fresh->role);
        $this->assertSame($position->id, $fresh->position_id);
        $this->assertSame(['projects'], $fresh->permissions);
    }

    public function test_new_users_default_to_member_role(): void
    {
        $user = User::factory()->create();

        $this->assertSame('member', $user->fresh()->role);
    }

    public function test_user_can_access_is_true_for_super_admin(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);

        $this->assertTrue($user->canAccess('blog'));
        $this->assertTrue($user->canAccess('team_management'));
    }

    public function test_user_can_access_honours_permissions_for_others(): void
    {
        $user = User::factory()->create([
            'role' => 'member',
            'permissions' => ['blog'],
        ]);

        $this->assertTrue($user->canAccess('blog'));
        $this->assertFalse($user->canAccess('todo'));
        $this->assertFalse($user->canAccess('team_management'));
    }

    public function test_is_super_admin_reflects_role(): void
    {
        $super = User::factory()->create(['role' => 'super_admin']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($super->isSuperAdmin());
        $this->assertFalse($admin->isSuperAdmin());
    }
}
