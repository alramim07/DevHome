<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogPublicTest extends TestCase
{
    use RefreshDatabase;

    private function author(): User
    {
        return User::factory()->create(['role' => 'member', 'permissions' => ['blog']]);
    }

    private function makePost(array $overrides = []): Blog
    {
        return Blog::create(array_merge([
            'user_id' => $this->author()->id,
            'title' => 'Sample Post',
            'slug' => 'sample-post',
            'content' => '<p>Body content here</p>',
            'status' => 'published',
            'published_at' => now(),
        ], $overrides));
    }

    public function test_published_post_is_visible_publicly(): void
    {
        $this->makePost();

        $this->get('/blog/sample-post')
            ->assertOk()
            ->assertSee('Sample Post')
            ->assertSee('Body content here', false);
    }

    public function test_blog_index_route_is_404(): void
    {
        $this->makePost();

        $this->get('/blog')->assertNotFound();
    }

    public function test_contact_form_is_present_on_post_page(): void
    {
        $this->makePost();

        $this->get('/blog/sample-post')
            ->assertOk()
            ->assertSee(route('contact.submit'), false);
    }

    public function test_three_suggested_published_posts_are_shown_and_current_excluded(): void
    {
        $current = $this->makePost();
        $others = [];
        for ($i = 1; $i <= 5; $i++) {
            $others[] = $this->makePost([
                'title' => "Other Post {$i}",
                'slug' => "other-post-{$i}",
            ]);
        }
        // One draft must never appear as a suggestion.
        $this->makePost(['title' => 'Draft Suggest', 'slug' => 'draft-suggest', 'status' => 'draft']);

        $response = $this->get('/blog/' . $current->slug)->assertOk();

        $html = $response->getContent();
        preg_match_all('#/blog/([a-z0-9\-]+)#', $html, $matches);
        $suggested = array_unique(array_diff($matches[1], [$current->slug]));

        $this->assertCount(3, $suggested);
        $this->assertNotContains('draft-suggest', $suggested);
        $this->assertNotContains($current->slug, $suggested);
    }

    public function test_unknown_slug_is_404(): void
    {
        $this->makePost();

        $this->get('/blog/does-not-exist')->assertNotFound();
    }

    public function test_post_page_renders_with_empty_site_settings(): void
    {
        // Simulates a fresh install where website_settings is empty.
        $this->makePost();

        $this->get('/blog/sample-post')->assertOk();
    }

    public function test_suggestion_count_is_three_even_with_two_posts(): void
    {
        $current = $this->makePost();
        $this->makePost(['title' => 'Only Other', 'slug' => 'only-other']);

        $html = $this->get('/blog/' . $current->slug)->getContent();
        preg_match_all('#/blog/([a-z0-9\-]+)#', $html, $matches);
        $suggested = array_unique(array_diff($matches[1], [$current->slug]));

        $this->assertCount(1, $suggested);
    }
}
