<?php

namespace Tests\Feature;

use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PositionCrudTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        return User::factory()->create(['role' => 'super_admin']);
    }

    public function test_super_admin_can_create_position_with_preset(): void
    {
        $this->actingAs($this->superAdmin())
            ->post(route('team.positions.store'), [
                'name' => 'QA Engineer',
                'permissions' => ['projects', 'todo'],
            ])
            ->assertRedirect();

        $position = Position::where('name', 'QA Engineer')->first();
        $this->assertNotNull($position);
        $this->assertSame(['projects', 'todo'], $position->permissions);
    }

    public function test_position_name_is_required_and_unique(): void
    {
        $admin = $this->superAdmin();
        Position::create(['name' => 'Developer', 'permissions' => []]);

        $this->actingAs($admin)
            ->post(route('team.positions.store'), ['name' => '', 'permissions' => []])
            ->assertSessionHasErrors('name');

        $this->actingAs($admin)
            ->post(route('team.positions.store'), ['name' => 'Developer', 'permissions' => []])
            ->assertSessionHasErrors('name');
    }

    public function test_permissions_must_be_known_sections(): void
    {
        $this->actingAs($this->superAdmin())
            ->post(route('team.positions.store'), [
                'name' => 'Hacker',
                'permissions' => ['not_a_section'],
            ])
            ->assertSessionHasErrors('permissions.0');
    }

    public function test_super_admin_can_update_position(): void
    {
        $position = Position::create(['name' => 'Old Name', 'permissions' => ['todo']]);

        $this->actingAs($this->superAdmin())
            ->post(route('team.positions.update', $position), [
                'name' => 'New Name',
                'permissions' => ['projects', 'blog'],
            ])
            ->assertRedirect();

        $fresh = $position->fresh();
        $this->assertSame('New Name', $fresh->name);
        $this->assertSame(['projects', 'blog'], $fresh->permissions);
    }

    public function test_cannot_update_position_to_duplicate_name(): void
    {
        Position::create(['name' => 'Developer', 'permissions' => []]);
        $other = Position::create(['name' => 'Designer', 'permissions' => []]);

        $this->actingAs($this->superAdmin())
            ->post(route('team.positions.update', $other), [
                'name' => 'Developer',
                'permissions' => [],
            ])
            ->assertSessionHasErrors('name');
    }

    public function test_deleting_position_nulls_user_position_id(): void
    {
        $position = Position::create(['name' => 'Temp', 'permissions' => []]);
        $user = User::factory()->create(['position_id' => $position->id]);

        $this->actingAs($this->superAdmin())
            ->post(route('team.positions.delete', $position))
            ->assertRedirect();

        $this->assertDatabaseMissing('positions', ['id' => $position->id]);
        $this->assertNull($user->fresh()->position_id);
    }

    public function test_non_super_admin_cannot_write_positions_even_with_section(): void
    {
        $member = User::factory()->create(['role' => 'member', 'permissions' => ['team_management']]);

        $this->actingAs($member)
            ->post(route('team.positions.store'), ['name' => 'Nope', 'permissions' => []])
            ->assertForbidden();

        $position = Position::create(['name' => 'Victim', 'permissions' => []]);
        $this->actingAs($member)
            ->post(route('team.positions.delete', $position))
            ->assertForbidden();
        $this->assertDatabaseHas('positions', ['id' => $position->id]);
    }
}
