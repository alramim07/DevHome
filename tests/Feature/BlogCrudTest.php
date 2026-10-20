<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogCrudTest extends TestCase
{
    use RefreshDatabase;

    private function author(): User
    {
        return User::factory()->create(['role' => 'member', 'permissions' => ['blog']]);
    }

    public function test_member_without_blog_section_gets_403(): void
    {
        $user = User::factory()->create(['role' => 'member', 'permissions' => ['todo']]);

        $this->actingAs($user)->get('/add-blog')->assertForbidden();
        $this->actingAs($user)->get('/blog-list')->assertForbidden();
    }

    public function test_member_with_blog_section_can_view(): void
    {
        $user = $this->author();

        $this->actingAs($user)->get('/add-blog')->assertOk();
        $this->actingAs($user)->get('/blog-list')->assertOk();
    }

    public function test_super_admin_can_view_blog_pages(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($user)->get('/add-blog')->assertOk();
        $this->actingAs($user)->get('/blog-list')->assertOk();
    }

    public function test_author_can_create_post(): void
    {
        $author = $this->author();

        $this->actingAs($author)
            ->post('/add-blog', [
                'title' => 'My First Blog Post',
                'content' => '<p>Hello world content</p>',
                'status' => 'published',
            ])
            ->assertRedirect(route('blog.list'));

        $post = Blog::where('title', 'My First Blog Post')->first();
        $this->assertNotNull($post);
        $this->assertSame($author->id, $post->user_id);
        $this->assertSame('my-first-blog-post', $post->slug);
        $this->assertSame('published', $post->status);
        $this->assertNotNull($post->published_at);
    }

    public function test_slug_is_unique_and_validation_applies(): void
    {
        $author = $this->author();
        Blog::create([
            'user_id' => $author->id,
            'title' => 'Existing',
            'slug' => 'existing-post',
            'content' => '<p>x</p>',
            'status' => 'published',
        ]);

        $this->actingAs($author)->post('/add-blog', [])->assertSessionHasErrors([
            'title', 'content', 'status',
        ]);

        $this->actingAs($author)
            ->post('/add-blog', [
                'title' => 'Another',
                'slug' => 'existing-post',
                'content' => '<p>y</p>',
                'status' => 'draft',
            ])
            ->assertSessionHasErrors('slug');

        $this->actingAs($author)
            ->post('/add-blog', [
                'title' => 'Bad status',
                'content' => '<p>z</p>',
                'status' => 'archived',
            ])
            ->assertSessionHasErrors('status');
    }

    public function test_author_can_update_and_delete_post(): void
    {
        $author = $this->author();
        $post = Blog::create([
            'user_id' => $author->id,
            'title' => 'Before',
            'slug' => 'before',
            'content' => '<p>old</p>',
            'status' => 'draft',
        ]);

        $this->actingAs($author)
            ->post("/edit-blog/{$post->id}/update", [
                'title' => 'After',
                'content' => '<p>new</p>',
                'status' => 'published',
            ])
            ->assertRedirect(route('blog.list'));

        $fresh = $post->fresh();
        $this->assertSame('After', $fresh->title);
        $this->assertSame('published', $fresh->status);
        $this->assertNotNull($fresh->published_at);

        $this->actingAs($author)
            ->post("/blog-list/{$post->id}/delete")
            ->assertRedirect(route('blog.list'));

        $this->assertDatabaseMissing('blogs', ['id' => $post->id]);
    }

    public function test_cover_image_can_be_uploaded(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $author = $this->author();

        $this->actingAs($author)
            ->post('/add-blog', [
                'title' => 'With Cover',
                'content' => '<p>c</p>',
                'status' => 'draft',
                'cover_image' => \Illuminate\Http\UploadedFile::fake()->image('cover.jpg', 100, 100),
            ])
            ->assertRedirect(route('blog.list'));

        $post = Blog::where('title', 'With Cover')->first();
        $this->assertNotNull($post->cover_image);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists(
            str_replace('storage/', '', $post->cover_image)
        );
    }

    public function test_draft_is_not_publicly_visible(): void
    {
        $author = $this->author();
        Blog::create([
            'user_id' => $author->id,
            'title' => 'Secret Draft',
            'slug' => 'secret-draft',
            'content' => '<p>hidden</p>',
            'status' => 'draft',
        ]);

        $this->get('/blog/secret-draft')->assertNotFound();
    }
}
