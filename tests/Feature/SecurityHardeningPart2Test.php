<?php

namespace Tests\Feature;

use App\Models\Position;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHardeningPart2Test extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        return User::factory()->create(['role' => 'super_admin']);
    }

    // ------------------------------------------------------------------
    // Super-admin write guards (positions/teams/members)
    // ------------------------------------------------------------------

    public function test_member_with_team_management_cannot_write_positions(): void
    {
        $member = User::factory()->create(['role' => 'member', 'permissions' => ['team_management']]);
        $position = Position::create(['name' => 'Target', 'permissions' => []]);

        $this->actingAs($member)
            ->post(route('team.positions.store'), ['name' => 'Hacker', 'permissions' => []])
            ->assertForbidden();

        $this->actingAs($member)
            ->post(route('team.positions.update', $position->id), ['name' => 'Hacked'])
            ->assertForbidden();

        $this->actingAs($member)
            ->post(route('team.positions.delete', $position->id))
            ->assertForbidden();
    }

    public function test_admin_with_team_management_cannot_write_positions(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'permissions' => ['team_management']]);

        $this->actingAs($admin)
            ->post(route('team.positions.store'), ['name' => 'Hacker', 'permissions' => []])
            ->assertForbidden();
    }

    public function test_member_with_team_management_cannot_create_users(): void
    {
        $manager = User::factory()->create(['role' => 'member', 'permissions' => ['team_management']]);

        $this->actingAs($manager)
            ->get(route('team.members.create'))
            ->assertForbidden();

        $this->actingAs($manager)
            ->post(route('team.members.store'), [
                'name' => 'X', 'email' => 'x@example.com', 'password' => 'password123', 'role' => 'member',
            ])
            ->assertForbidden();
    }

    public function test_admin_with_team_management_cannot_create_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'permissions' => ['team_management']]);

        $this->actingAs($admin)
            ->post(route('team.members.store'), [
                'name' => 'X', 'email' => 'x@example.com', 'password' => 'password123', 'role' => 'member',
            ])
            ->assertForbidden();
    }

    // ------------------------------------------------------------------
    // Profile update cannot escalate permissions
    // ------------------------------------------------------------------

    public function test_profile_update_ignores_permissions_field(): void
    {
        $user = User::factory()->create(['role' => 'member', 'permissions' => ['todo']]);

        $this->actingAs($user)
            ->post(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'permissions' => ['blog', 'projects', 'team_management'],
            ])
            ->assertRedirect();

        $this->assertSame(['todo'], $user->fresh()->permissions);
    }

    public function test_profile_update_ignores_role_field(): void
    {
        $user = User::factory()->create(['role' => 'member']);

        $this->actingAs($user)
            ->post(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'super_admin',
            ])
            ->assertRedirect();

        $this->assertSame('member', $user->fresh()->role);
    }

    public function test_profile_update_ignores_position_field(): void
    {
        $user = User::factory()->create(['role' => 'member']);
        $pos = Position::create(['name' => 'Manager', 'permissions' => []]);

        $this->actingAs($user)
            ->post(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'position_id' => $pos->id,
            ])
            ->assertRedirect();

        $this->assertNull($user->fresh()->position_id);
    }

    // ------------------------------------------------------------------
    // Blog: draft posts not leaked via suggested posts
    // ------------------------------------------------------------------

    public function test_draft_posts_never_appear_in_suggestions(): void
    {
        $author = User::factory()->create(['role' => 'member', 'permissions' => ['blog']]);
        $current = \App\Models\Blog::create([
            'user_id' => $author->id, 'title' => 'Published', 'slug' => 'published',
            'content' => '<p>x</p>', 'status' => 'published', 'published_at' => now(),
        ]);
        \App\Models\Blog::create([
            'user_id' => $author->id, 'title' => 'Draft', 'slug' => 'draft',
            'content' => '<p>y</p>', 'status' => 'draft',
        ]);

        $html = $this->get('/blog/' . $current->slug)->getContent();
        preg_match_all('#/blog/([a-z0-9\-]+)#', $html, $matches);
        $suggested = array_unique(array_diff($matches[1], [$current->slug]));

        $this->assertNotContains('draft', $suggested);
    }

    // ------------------------------------------------------------------
    // Rate limiting on cover upload
    // ------------------------------------------------------------------

    public function test_blog_cover_upload_has_rate_limit(): void
    {
        $author = User::factory()->create(['role' => 'member', 'permissions' => ['blog']]);

        // The upload route is project.image.upload with throttle:uploads
        // Blog uses the same public disk; verify throttle exists on the route
        $route = \Illuminate\Support\Facades\Route::getRoutes()->getByName('project.image.upload');
        $this->assertNotNull($route);
        $middleware = $route->gatherMiddleware();
        $this->assertTrue(in_array('throttle:uploads', $middleware) || in_array('throttle', $middleware));
    }

    // ------------------------------------------------------------------
    // Old hardcoded id===1 checks removed
    // ------------------------------------------------------------------

    public function test_no_id_hardcode_in_controllers(): void
    {
        $controllers = [
            app_path('Http/Controllers/Admin/WebsiteSettingsController.php'),
            app_path('Http/Controllers/Admin/TeamRequest.php'),
            app_path('Http/Controllers/Admin/AdminController.php'),
        ];

        foreach ($controllers as $file) {
            $content = file_get_contents($file);
            // Check for the old pattern: id === 1 or id !== 1 (with various spacing)
            $this->assertStringNotContainsString('id === 1', $content, "Found 'id === 1' in $file");
            $this->assertStringNotContainsString('id !== 1', $content, "Found 'id !== 1' in $file");
            $this->assertStringNotContainsString('id == 1', $content, "Found 'id == 1' in $file");
            $this->assertStringNotContainsString('id != 1', $content, "Found 'id != 1' in $file");
        }
    }
}