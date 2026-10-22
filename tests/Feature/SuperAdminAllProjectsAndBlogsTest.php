<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\Projects;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminAllProjectsAndBlogsTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_view_my_and_all_projects_via_ajax(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'permissions' => ['projects'],
        ]);

        $otherUser = User::factory()->create([
            'role' => 'member',
            'permissions' => ['projects'],
        ]);

        $myProject = Projects::create([
            'user_id' => $superAdmin->id,
            'title' => 'Super Admin Project',
            'slug' => 'super-admin-project',
            'description' => 'Test description',
        ]);

        $otherProject = Projects::create([
            'user_id' => $otherUser->id,
            'title' => 'Other Member Project',
            'slug' => 'other-member-project',
            'description' => 'Other description',
        ]);

        // 1. Initial page load as super admin shows My Projects and the toggle button
        $response = $this->actingAs($superAdmin)->get(route('list.project'));
        $response->assertOk();
        $response->assertSee('Super Admin Project');
        $response->assertDontSee('Other Member Project');
        $response->assertSee('btn-toggle-projects-scope');
        $response->assertSee('See All Projects');

        // 2. AJAX request with scope=all returns all projects on entire website
        $ajaxResponse = $this->actingAs($superAdmin)->getJson(route('list.project', ['scope' => 'all']));
        $ajaxResponse->assertOk();
        $ajaxResponse->assertJsonStructure(['scope', 'count', 'html']);
        $this->assertSame('all', $ajaxResponse->json('scope'));
        $this->assertSame(2, $ajaxResponse->json('count'));
        $this->assertStringContainsString('Super Admin Project', $ajaxResponse->json('html'));
        $this->assertStringContainsString('Other Member Project', $ajaxResponse->json('html'));
        $this->assertStringContainsString($otherUser->name, $ajaxResponse->json('html'));

        // 3. AJAX request with scope=my switches back to my projects
        $ajaxMy = $this->actingAs($superAdmin)->getJson(route('list.project', ['scope' => 'my']));
        $ajaxMy->assertOk();
        $this->assertSame('my', $ajaxMy->json('scope'));
        $this->assertSame(1, $ajaxMy->json('count'));
        $this->assertStringContainsString('Super Admin Project', $ajaxMy->json('html'));
        $this->assertStringNotContainsString('Other Member Project', $ajaxMy->json('html'));
    }

    public function test_non_super_admin_cannot_see_all_projects_button_or_scope_all(): void
    {
        $member = User::factory()->create([
            'role' => 'member',
            'permissions' => ['projects'],
        ]);

        $otherUser = User::factory()->create([
            'role' => 'member',
            'permissions' => ['projects'],
        ]);

        Projects::create([
            'user_id' => $member->id,
            'title' => 'Member Own Project',
            'slug' => 'member-own-project',
            'description' => 'Desc',
        ]);

        Projects::create([
            'user_id' => $otherUser->id,
            'title' => 'Other Secret Project',
            'slug' => 'other-secret-project',
            'description' => 'Desc',
        ]);

        // Initial page load doesn't show See All Projects button
        $response = $this->actingAs($member)->get(route('list.project'));
        $response->assertOk();
        $response->assertSee('Member Own Project');
        $response->assertDontSee('Other Secret Project');
        $response->assertDontSee('btn-toggle-projects-scope');
        $response->assertDontSee('See All Projects');

        // If member tries scope=all, it falls back to my scope and only their own projects
        $ajaxResponse = $this->actingAs($member)->getJson(route('list.project', ['scope' => 'all']));
        $ajaxResponse->assertOk();
        $this->assertSame('my', $ajaxResponse->json('scope'));
        $this->assertSame(1, $ajaxResponse->json('count'));
        $this->assertStringContainsString('Member Own Project', $ajaxResponse->json('html'));
        $this->assertStringNotContainsString('Other Secret Project', $ajaxResponse->json('html'));
    }

    public function test_super_admin_can_view_my_and_all_blogs_via_ajax(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'permissions' => ['blog'],
        ]);

        $otherUser = User::factory()->create([
            'role' => 'member',
            'permissions' => ['blog'],
        ]);

        Blog::create([
            'user_id' => $superAdmin->id,
            'title' => 'Super Admin Blog Post',
            'slug' => 'super-admin-blog-post',
            'content' => '<p>Super admin post</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        Blog::create([
            'user_id' => $otherUser->id,
            'title' => 'Other Member Blog Post',
            'slug' => 'other-member-blog-post',
            'content' => '<p>Other member post</p>',
            'status' => 'draft',
        ]);

        // 1. Initial page load as super admin shows My Blogs and the toggle button
        $response = $this->actingAs($superAdmin)->get(route('blog.list'));
        $response->assertOk();
        $response->assertSee('Super Admin Blog Post');
        $response->assertDontSee('Other Member Blog Post');
        $response->assertSee('btn-toggle-blogs-scope');
        $response->assertSee('See All Blogs');

        // 2. AJAX request with scope=all returns all blogs on entire website
        $ajaxResponse = $this->actingAs($superAdmin)->getJson(route('blog.list', ['scope' => 'all']));
        $ajaxResponse->assertOk();
        $ajaxResponse->assertJsonStructure(['scope', 'count', 'html']);
        $this->assertSame('all', $ajaxResponse->json('scope'));
        $this->assertSame(2, $ajaxResponse->json('count'));
        $this->assertStringContainsString('Super Admin Blog Post', $ajaxResponse->json('html'));
        $this->assertStringContainsString('Other Member Blog Post', $ajaxResponse->json('html'));
        $this->assertStringContainsString($otherUser->name, $ajaxResponse->json('html'));

        // 3. AJAX request with scope=my switches back to my blogs
        $ajaxMy = $this->actingAs($superAdmin)->getJson(route('blog.list', ['scope' => 'my']));
        $ajaxMy->assertOk();
        $this->assertSame('my', $ajaxMy->json('scope'));
        $this->assertSame(1, $ajaxMy->json('count'));
        $this->assertStringContainsString('Super Admin Blog Post', $ajaxMy->json('html'));
        $this->assertStringNotContainsString('Other Member Blog Post', $ajaxMy->json('html'));
    }

    public function test_non_super_admin_cannot_see_all_blogs_button_or_scope_all(): void
    {
        $member = User::factory()->create([
            'role' => 'member',
            'permissions' => ['blog'],
        ]);

        $otherUser = User::factory()->create([
            'role' => 'member',
            'permissions' => ['blog'],
        ]);

        Blog::create([
            'user_id' => $member->id,
            'title' => 'Member Own Blog Post',
            'slug' => 'member-own-blog-post',
            'content' => '<p>Member post</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        Blog::create([
            'user_id' => $otherUser->id,
            'title' => 'Other Member Secret Blog',
            'slug' => 'other-member-secret-blog',
            'content' => '<p>Secret post</p>',
            'status' => 'draft',
        ]);

        // Initial page load doesn't show See All Blogs button
        $response = $this->actingAs($member)->get(route('blog.list'));
        $response->assertOk();
        $response->assertSee('Member Own Blog Post');
        $response->assertDontSee('Other Member Secret Blog');
        $response->assertDontSee('btn-toggle-blogs-scope');
        $response->assertDontSee('See All Blogs');

        // If member tries scope=all, it falls back to my scope and only their own blogs
        $ajaxResponse = $this->actingAs($member)->getJson(route('blog.list', ['scope' => 'all']));
        $ajaxResponse->assertOk();
        $this->assertSame('my', $ajaxResponse->json('scope'));
        $this->assertSame(1, $ajaxResponse->json('count'));
        $this->assertStringContainsString('Member Own Blog Post', $ajaxResponse->json('html'));
        $this->assertStringNotContainsString('Other Member Secret Blog', $ajaxResponse->json('html'));
    }

    public function test_super_admin_can_view_my_and_all_emails_via_ajax(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'permissions' => ['email_list'],
        ]);

        $otherUser = User::factory()->create([
            'role' => 'member',
            'permissions' => ['email_list'],
        ]);

        \App\Models\Email::create([
            'user_id' => $superAdmin->id,
            'first_name' => 'Alice',
            'last_name' => 'Admin',
            'email' => 'alice@example.com',
            'message' => 'Hello Super Admin',
        ]);

        \App\Models\Email::create([
            'user_id' => $otherUser->id,
            'first_name' => 'Bob',
            'last_name' => 'Member',
            'email' => 'bob@example.com',
            'message' => 'Hello Other Member',
        ]);

        // 1. Initial page load as super admin shows My Emails and the toggle button
        $response = $this->actingAs($superAdmin)->get(route('email.list'));
        $response->assertOk();
        $response->assertSee('Alice');
        $response->assertDontSee('Bob');
        $response->assertSee('btn-toggle-emails-scope');
        $response->assertSee('See All Emails');

        // 2. AJAX request with scope=all returns all emails on entire website
        $ajaxResponse = $this->actingAs($superAdmin)->getJson(route('email.list', ['scope' => 'all']));
        $ajaxResponse->assertOk();
        $ajaxResponse->assertJsonStructure(['scope', 'count', 'html']);
        $this->assertSame('all', $ajaxResponse->json('scope'));
        $this->assertSame(2, $ajaxResponse->json('count'));
        $this->assertStringContainsString('Alice', $ajaxResponse->json('html'));
        $this->assertStringContainsString('Bob', $ajaxResponse->json('html'));
        $this->assertStringContainsString($otherUser->name, $ajaxResponse->json('html'));

        // 3. AJAX request with scope=my switches back to my emails
        $ajaxMy = $this->actingAs($superAdmin)->getJson(route('email.list', ['scope' => 'my']));
        $ajaxMy->assertOk();
        $this->assertSame('my', $ajaxMy->json('scope'));
        $this->assertSame(1, $ajaxMy->json('count'));
        $this->assertStringContainsString('Alice', $ajaxMy->json('html'));
        $this->assertStringNotContainsString('Bob', $ajaxMy->json('html'));
    }

    public function test_non_super_admin_cannot_see_all_emails_button_or_scope_all(): void
    {
        $member = User::factory()->create([
            'role' => 'member',
            'permissions' => ['email_list'],
        ]);

        $otherUser = User::factory()->create([
            'role' => 'member',
            'permissions' => ['email_list'],
        ]);

        \App\Models\Email::create([
            'user_id' => $member->id,
            'first_name' => 'MemberContact',
            'last_name' => 'One',
            'email' => 'm1@example.com',
            'message' => 'Member mail',
        ]);

        \App\Models\Email::create([
            'user_id' => $otherUser->id,
            'first_name' => 'SecretContact',
            'last_name' => 'Two',
            'email' => 's2@example.com',
            'message' => 'Secret mail',
        ]);

        // Initial page load doesn't show See All Emails button
        $response = $this->actingAs($member)->get(route('email.list'));
        $response->assertOk();
        $response->assertSee('MemberContact');
        $response->assertDontSee('SecretContact');
        $response->assertDontSee('btn-toggle-emails-scope');
        $response->assertDontSee('See All Emails');

        // If member tries scope=all, it falls back to my scope and only their own emails
        $ajaxResponse = $this->actingAs($member)->getJson(route('email.list', ['scope' => 'all']));
        $ajaxResponse->assertOk();
        $this->assertSame('my', $ajaxResponse->json('scope'));
        $this->assertSame(1, $ajaxResponse->json('count'));
        $this->assertStringContainsString('MemberContact', $ajaxResponse->json('html'));
        $this->assertStringNotContainsString('SecretContact', $ajaxResponse->json('html'));
    }

    public function test_single_project_ajax_delete_works(): void
    {
        $user = User::factory()->create([
            'role' => 'member',
            'permissions' => ['projects'],
        ]);

        $project = Projects::create([
            'user_id' => $user->id,
            'title' => 'Delete Me Project',
            'slug' => 'delete-me-project',
            'description' => 'Desc',
        ]);

        $response = $this->actingAs($user)->postJson(route('projects.destroy', $project->slug));
        $response->assertOk();
        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('projects', ['slug' => 'delete-me-project']);
    }

    public function test_bulk_project_ajax_delete_works(): void
    {
        $user = User::factory()->create([
            'role' => 'member',
            'permissions' => ['projects'],
        ]);

        $p1 = Projects::create([
            'user_id' => $user->id,
            'title' => 'Bulk Project 1',
            'slug' => 'bulk-project-1',
            'description' => 'Desc',
        ]);

        $p2 = Projects::create([
            'user_id' => $user->id,
            'title' => 'Bulk Project 2',
            'slug' => 'bulk-project-2',
            'description' => 'Desc',
        ]);

        $response = $this->actingAs($user)->postJson(route('projects.bulk-delete'), [
            'slugs' => [$p1->slug, $p2->slug],
        ]);
        $response->assertOk();
        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('projects', ['slug' => 'bulk-project-1']);
        $this->assertDatabaseMissing('projects', ['slug' => 'bulk-project-2']);
    }

    public function test_single_blog_ajax_delete_works(): void
    {
        $user = User::factory()->create([
            'role' => 'member',
            'permissions' => ['blog'],
        ]);

        $blog = Blog::create([
            'user_id' => $user->id,
            'title' => 'Delete Me Blog',
            'slug' => 'delete-me-blog',
            'content' => 'content',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)->postJson(route('blog.destroy', $blog->id));
        $response->assertOk();
        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('blogs', ['id' => $blog->id]);
    }

    public function test_bulk_blog_ajax_delete_works(): void
    {
        $user = User::factory()->create([
            'role' => 'member',
            'permissions' => ['blog'],
        ]);

        $b1 = Blog::create([
            'user_id' => $user->id,
            'title' => 'Bulk Blog 1',
            'slug' => 'bulk-blog-1',
            'content' => 'content',
            'status' => 'draft',
        ]);

        $b2 = Blog::create([
            'user_id' => $user->id,
            'title' => 'Bulk Blog 2',
            'slug' => 'bulk-blog-2',
            'content' => 'content',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)->postJson(route('blog.bulk-delete'), [
            'ids' => [$b1->id, $b2->id],
        ]);
        $response->assertOk();
        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('blogs', ['id' => $b1->id]);
        $this->assertDatabaseMissing('blogs', ['id' => $b2->id]);
    }
}
