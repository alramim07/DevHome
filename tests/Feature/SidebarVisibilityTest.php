<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SidebarVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_with_no_permissions_sees_only_dashboard_and_profile(): void
    {
        $user = User::factory()->create(['role' => 'member', 'permissions' => []]);

        $response = $this->actingAs($user)->get('/dashboard')->assertOk();

        // Sidebar should NOT show section-gated navigation links
        // Check for the specific sidebar nav-item structure to avoid false positives
        // from dashboard content links (e.g., "Show All" buttons that use the same routes)
        $html = $response->getContent();
        // The test framework doesn't have DOM parsing, so check for unique sidebar patterns:
        // Projects dropdown, Blog dropdown, Website Settings, E-mails (with keyboard icon),
        // Website Contacts, Team Management dropdown, Join Requests, To Do List
        // Check for the sidebar link text + icon combination to avoid matching dashboard cards
        $this->assertStringNotContainsString('fa fa-laptop me-2', $html); // Projects
        $this->assertStringNotContainsString('fa fa-newspaper me-2', $html); // Blog
        $this->assertStringNotContainsString('fa fa-th me-2', $html); // Website Settings
        $this->assertStringNotContainsString('fa fa-keyboard me-2', $html); // E-mails / Website Contacts
        $this->assertStringNotContainsString('fa fa-users me-2', $html); // Team Management
        $this->assertStringNotContainsString('fa fa-table me-2', $html); // Join Requests / To Do List

        // Dashboard should be present
        $this->assertStringContainsString('fa fa-tachometer-alt me-2', $html);
    }

    public function test_member_with_blog_permission_sees_blog_links_only(): void
    {
        $user = User::factory()->create(['role' => 'member', 'permissions' => ['blog']]);

        $response = $this->actingAs($user)->get('/dashboard')->assertOk();

        $html = $response->getContent();
        $this->assertStringContainsString('/add-blog', $html);
        $this->assertStringContainsString('/blog-list', $html);

        // Other sections still hidden
        $this->assertStringNotContainsString('/add-project', $html);
        $this->assertStringNotContainsString('/website-settings', $html);
        $this->assertStringNotContainsString('/positions', $html);
    }

    public function test_member_with_team_management_sees_team_management_dropdown(): void
    {
        $user = User::factory()->create(['role' => 'member', 'permissions' => ['team_management']]);

        $response = $this->actingAs($user)->get('/dashboard')->assertOk();

        $html = $response->getContent();
        $this->assertStringContainsString('/positions', $html);
        $this->assertStringContainsString('/teams', $html);
        $this->assertStringContainsString('/members', $html);

        // Create User link should NOT appear for non-super-admin
        $this->assertStringNotContainsString('/members/create', $html);
    }

    public function test_super_admin_sees_all_sections_including_create_user(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);

        $response = $this->actingAs($user)->get('/dashboard')->assertOk();

        $html = $response->getContent();
        // Projects
        $this->assertStringContainsString('/add-project', $html);
        $this->assertStringContainsString('/project-list', $html);
        // Blog
        $this->assertStringContainsString('/add-blog', $html);
        $this->assertStringContainsString('/blog-list', $html);
        // Team Management
        $this->assertStringContainsString('/positions', $html);
        $this->assertStringContainsString('/teams', $html);
        $this->assertStringContainsString('/members', $html);
        // Super-admin-only Create User
        $this->assertStringContainsString('/members/create', $html);
        // Other sections
        $this->assertStringContainsString('/website-settings', $html);
        $this->assertStringContainsString('/website-contacts', $html);
        $this->assertStringContainsString('/email-list', $html);
        $this->assertStringContainsString('/join-list', $html);
        $this->assertStringContainsString('/todo', $html);
    }

    public function test_debug_dashboard_route_returns_404(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($user)->get('/debug-dashboard')->assertNotFound();
    }
}