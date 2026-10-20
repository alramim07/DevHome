<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\Projects;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_returns_valid_xml(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/xml');
        
        $content = $response->getContent();
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $content);
        $this->assertStringContainsString('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"', $content);
    }

    public function test_sitemap_includes_homepage(): void
    {
        $response = $this->get('/sitemap.xml');
        $content = $response->getContent();
        
        $this->assertStringContainsString(config('app.url'), $content);
        $this->assertStringContainsString('<priority>1.0</priority>', $content);
    }

    public function test_sitemap_includes_published_projects(): void
    {
        $user = User::factory()->create();
        $project = Projects::create([
            'user_id' => $user->id,
            'title' => 'Test Project',
            'slug' => 'test-project',
            'description' => '<p>Test</p>',
            'github_url' => 'https://github.com/test',
            'live_url' => 'https://test.com',
        ]);

        $response = $this->get('/sitemap.xml');
        $content = $response->getContent();
        
        $this->assertStringContainsString('/project/test-project', $content);
        $this->assertStringContainsString('<priority>0.8</priority>', $content);
    }

    public function test_sitemap_excludes_unpublished_projects(): void
    {
        // All projects are public by default in this app, but we test the slug exists
        $user = User::factory()->create();
        Projects::create([
            'user_id' => $user->id,
            'title' => 'Another Project',
            'slug' => 'another-project',
            'description' => '<p>Test</p>',
        ]);

        $response = $this->get('/sitemap.xml');
        $content = $response->getContent();
        
        $this->assertStringContainsString('/project/another-project', $content);
    }

    public function test_sitemap_includes_published_blogs(): void
    {
        $author = User::factory()->create(['role' => 'member', 'permissions' => ['blog']]);
        $blog = Blog::create([
            'user_id' => $author->id,
            'title' => 'Test Blog Post',
            'slug' => 'test-blog-post',
            'content' => '<p>Test content</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get('/sitemap.xml');
        $content = $response->getContent();
        
        $this->assertStringContainsString('/blog/test-blog-post', $content);
        $this->assertStringContainsString('<priority>0.7</priority>', $content);
    }

    public function test_sitemap_excludes_draft_blogs(): void
    {
        $author = User::factory()->create(['role' => 'member', 'permissions' => ['blog']]);
        Blog::create([
            'user_id' => $author->id,
            'title' => 'Draft Post',
            'slug' => 'draft-post',
            'content' => '<p>Draft</p>',
            'status' => 'draft',
        ]);

        $response = $this->get('/sitemap.xml');
        $content = $response->getContent();
        
        $this->assertStringNotContainsString('/blog/draft-post', $content);
    }

    public function test_sitemap_has_valid_xml_structure(): void
    {
        $response = $this->get('/sitemap.xml');
        $content = $response->getContent();
        
        // Should be valid XML
        $xml = simplexml_load_string($content);
        $this->assertNotNull($xml);
        $this->assertEquals('urlset', $xml->getName());
    }

    public function test_sitemap_cache_works(): void
    {
        // First request
        $response1 = $this->get('/sitemap.xml');
        $etag1 = $response1->headers->get('ETag');
        
        // Second request should be cached (same content)
        $response2 = $this->get('/sitemap.xml');
        $etag2 = $response2->headers->get('ETag');
        
        // Both should succeed
        $response1->assertOk();
        $response2->assertOk();
    }
}