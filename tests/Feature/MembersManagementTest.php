<?php

namespace Tests\Feature;

use App\Models\Position;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MembersManagementTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        return User::factory()->create(['role' => 'super_admin']);
    }

    // ------------------------------------------------------------------
    // Access
    // ------------------------------------------------------------------

    public function test_member_without_team_management_gets_403_on_members(): void
    {
        $user = User::factory()->create(['role' => 'member', 'permissions' => ['todo']]);

        $this->actingAs($user)->get('/members')->assertForbidden();
    }

    public function test_member_with_team_management_can_view_members(): void
    {
        $user = User::factory()->create(['role' => 'member', 'permissions' => ['team_management']]);

        $this->actingAs($user)->get('/members')->assertOk();
    }

    public function test_non_super_admin_cannot_create_user_even_with_section(): void
    {
        $member = User::factory()->create(['role' => 'admin', 'permissions' => ['team_management']]);

        $this->actingAs($member)->get('/members/create')->assertForbidden();
        $this->actingAs($member)->post('/members', [
            'name' => 'X', 'email' => 'x@example.com', 'password' => 'password123',
        ])->assertForbidden();
    }

    // ------------------------------------------------------------------
    // Create
    // ------------------------------------------------------------------

    public function test_super_admin_can_create_user(): void
    {
        Storage::fake('public');
        $position = Position::create(['name' => 'Digital Marketer', 'permissions' => ['blog', 'projects']]);
        $team = Team::create(['name' => 'Marketing']);

        $this->actingAs($this->superAdmin())
            ->post('/members', [
                'name' => 'New Marketer',
                'email' => 'marketer@example.com',
                'password' => 'secret-pass-1',
                'role' => 'member',
                'position_id' => $position->id,
                'team_ids' => [$team->id],
                'permissions' => ['blog'],
                'image' => UploadedFile::fake()->image('avatar.jpg', 10, 10),
            ])
            ->assertRedirect(route('team.members'));

        $user = User::where('email', 'marketer@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('member', $user->role);
        $this->assertSame($position->id, $user->position_id);
        $this->assertSame(['blog'], $user->permissions);
        $this->assertSame('Digital Marketer', $user->designation);
        $this->assertTrue($user->teams->contains('id', $team->id));
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('secret-pass-1', $user->password));
        $this->assertNotNull($user->slug);
    }

    public function test_create_user_validates_input(): void
    {
        $admin = $this->superAdmin();

        $this->actingAs($admin)->post('/members', [])->assertSessionHasErrors([
            'name', 'email', 'password', 'role',
        ]);

        $this->actingAs($admin)->post('/members', [
            'name' => 'Bad', 'email' => 'not-an-email', 'password' => 'short', 'role' => 'root',
        ])->assertSessionHasErrors(['email', 'password', 'role']);

        $this->actingAs($admin)->post('/members', [
            'name' => 'Bad2', 'email' => 'bad2@example.com', 'password' => 'password123',
            'role' => 'member', 'permissions' => ['not_a_section'],
        ])->assertSessionHasErrors('permissions.0');
    }

    public function test_create_user_rejects_duplicate_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->actingAs($this->superAdmin())
            ->post('/members', [
                'name' => 'Dupe', 'email' => 'taken@example.com', 'password' => 'password123', 'role' => 'member',
            ])
            ->assertSessionHasErrors('email');
    }

    // ------------------------------------------------------------------
    // Edit
    // ------------------------------------------------------------------

    public function test_super_admin_can_update_user_permissions_and_role(): void
    {
        $user = User::factory()->create(['role' => 'member', 'permissions' => ['todo']]);

        $this->actingAs($this->superAdmin())
            ->post("/members/{$user->id}/update", [
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'admin',
                'permissions' => ['projects', 'blog'],
            ])
            ->assertRedirect(route('team.members'));

        $fresh = $user->fresh();
        $this->assertSame('admin', $fresh->role);
        $this->assertSame(['projects', 'blog'], $fresh->permissions);
    }

    public function test_update_without_password_keeps_existing(): void
    {
        $user = User::factory()->create(['role' => 'member', 'password' => bcrypt('original-pass')]);

        $this->actingAs($this->superAdmin())
            ->post("/members/{$user->id}/update", [
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'member',
            ]);

        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('original-pass', $user->fresh()->password));
    }

    public function test_update_with_password_changes_it(): void
    {
        $user = User::factory()->create(['role' => 'member', 'password' => bcrypt('original-pass')]);

        $this->actingAs($this->superAdmin())
            ->post("/members/{$user->id}/update", [
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'member',
                'password' => 'new-secret-99',
            ]);

        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('new-secret-99', $user->fresh()->password));
    }

    // ------------------------------------------------------------------
    // Super-admin mutual protection
    // ------------------------------------------------------------------

    public function test_super_admin_cannot_change_another_super_admin(): void
    {
        $acting = $this->superAdmin();
        $other = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($acting)
            ->post("/members/{$other->id}/update", [
                'name' => $other->name,
                'email' => $other->email,
                'role' => 'admin',
            ])
            ->assertForbidden();

        $this->assertSame('super_admin', $other->fresh()->role);
    }

    public function test_super_admin_cannot_delete_another_super_admin(): void
    {
        $acting = $this->superAdmin();
        $other = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($acting)
            ->post("/members/{$other->id}/delete")
            ->assertForbidden();

        $this->assertNull($other->fresh()->deleted_at);
    }

    public function test_super_admin_cannot_delete_self(): void
    {
        $acting = $this->superAdmin();

        $this->from(route('team.members'))
            ->actingAs($acting)
            ->post("/members/{$acting->id}/delete")
            ->assertSessionHas('error');

        $this->assertNull($acting->fresh()->deleted_at);
    }

    // ------------------------------------------------------------------
    // Delete / restore
    // ------------------------------------------------------------------

    public function test_super_admin_can_soft_delete_and_restore_member(): void
    {
        $acting = $this->superAdmin();
        $member = User::factory()->create(['role' => 'member']);

        $this->actingAs($acting)->post("/members/{$member->id}/delete")->assertRedirect(route('team.members'));
        $this->assertSoftDeleted('users', ['id' => $member->id]);

        $this->actingAs($acting)->post("/members/{$member->id}/restore")->assertRedirect(route('team.members'));
        $this->assertNull($member->fresh()->deleted_at);
    }

    public function test_member_with_section_cannot_delete_users(): void
    {
        $manager = User::factory()->create(['role' => 'member', 'permissions' => ['team_management']]);
        $victim = User::factory()->create(['role' => 'member']);

        $this->actingAs($manager)->post("/members/{$victim->id}/delete")->assertForbidden();
        $this->assertNull($victim->fresh()->deleted_at);
    }

    // ------------------------------------------------------------------
    // Filters
    // ------------------------------------------------------------------

    public function test_filters_by_position_team_and_status(): void
    {
        $admin = $this->superAdmin();
        $devPos = Position::create(['name' => 'Developer', 'permissions' => []]);
        $designer = User::factory()->create(['role' => 'member', 'position_id' => null, 'name' => 'No Pos User']);
        $dev = User::factory()->create(['role' => 'member', 'position_id' => $devPos->id, 'name' => 'Dev User']);
        $team = Team::create(['name' => 'Frontend']);
        $team->users()->attach($dev->id);
        $trashed = User::factory()->create(['role' => 'member', 'name' => 'Gone User']);
        $trashed->delete();

        $r1 = $this->actingAs($admin)->get('/members?position_id=' . $devPos->id);
        $r1->assertOk()->assertSee('Dev User')->assertDontSee('No Pos User');

        $r2 = $this->actingAs($admin)->get('/members?team_id=' . $team->id);
        $r2->assertOk()->assertSee('Dev User')->assertDontSee('No Pos User');

        $r3 = $this->actingAs($admin)->get('/members?status=deleted');
        $r3->assertOk()->assertSee('Gone User')->assertDontSee('Dev User');
    }

    // ------------------------------------------------------------------
    // Preset pre-fill
    // ------------------------------------------------------------------

    public function test_create_form_prefills_from_position_preset(): void
    {
        $position = Position::create(['name' => 'Marketer', 'permissions' => ['blog', 'email_list']]);

        $r = $this->actingAs($this->superAdmin())->get('/members/create?position_id=' . $position->id);

        $r->assertOk();
        $html = $r->getContent();
        // The Blog + E-mails checkboxes for this position are checked.
        $this->assertMatchesRegularExpression(
            '/name="permissions\[\]"[^>]*value="blog"[^>]*checked|value="blog"[^>]*name="permissions\[\]"[^>]*checked/',
            $html
        );
    }

    public function test_super_admin_can_delete_member_via_ajax(): void
    {
        $admin = $this->superAdmin();
        $target = User::factory()->create(['role' => 'member']);

        $res = $this->actingAs($admin)->postJson("/members/{$target->id}/delete");
        $res->assertOk()->assertJson([
            'success' => true,
        ]);

        $this->assertSoftDeleted('users', ['id' => $target->id]);
    }

    public function test_super_admin_can_bulk_delete_members(): void
    {
        $admin = $this->superAdmin();
        $target1 = User::factory()->create(['role' => 'member']);
        $target2 = User::factory()->create(['role' => 'member']);
        $otherSuper = User::factory()->create(['role' => 'super_admin']);

        $res = $this->actingAs($admin)->postJson('/members/bulk-delete', [
            'ids' => [$target1->id, $target2->id, $admin->id, $otherSuper->id],
        ]);

        $res->assertOk()->assertJson([
            'success' => true,
            'deleted_count' => 2,
        ]);

        $this->assertSoftDeleted('users', ['id' => $target1->id]);
        $this->assertSoftDeleted('users', ['id' => $target2->id]);
        $this->assertNotSoftDeleted('users', ['id' => $admin->id]);
        $this->assertNotSoftDeleted('users', ['id' => $otherSuper->id]);
    }

    public function test_super_admin_can_update_member_status(): void
    {
        $admin = $this->superAdmin();
        $target = User::factory()->create(['role' => 'member', 'status' => 'active']);

        $res = $this->actingAs($admin)->postJson("/members/{$target->id}/status", [
            'status' => 'suspended',
        ]);

        $res->assertOk()->assertJson([
            'success' => true,
            'status' => 'suspended',
            'status_label' => 'Suspanded',
        ]);

        $this->assertEquals('suspended', $target->fresh()->status);
    }

    public function test_suspended_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'email' => 'suspended@example.com',
            'password' => 'secret123',
            'status' => 'suspended',
        ]);

        $res = $this->post('/login', [
            'email' => 'suspended@example.com',
            'password' => 'secret123',
        ]);

        $res->assertSessionHasErrors(['email' => 'Your account has been suspended. Please contact the administrator.']);
        $this->assertGuest();
    }

    // ------------------------------------------------------------------
    // Old routes removed
    // ------------------------------------------------------------------

    public function test_old_dev_routes_are_gone_with_redirects(): void
    {
        $admin = $this->superAdmin();

        $this->actingAs($admin)->get('/dev-list')->assertRedirect(route('team.members'));
        $this->actingAs($admin)->get('/add-dev')->assertRedirect(route('team.members.create'));
    }
}
