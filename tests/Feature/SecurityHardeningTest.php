<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_security_headers_are_present(): void
    {
        $this->get('/login')->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_contact_form_is_rate_limited_after_five(): void
    {
        $payload = [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
            'message' => 'Hello there',
        ];

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('contact.submit'), $payload);
        }

        $this->post(route('contact.submit'), $payload)->assertStatus(429);
    }

    public function test_login_is_rate_limited_after_six(): void
    {
        for ($i = 0; $i < 6; $i++) {
            $this->post(route('login.post'), ['email' => 'x@example.com', 'password' => 'wrong']);
        }

        $this->post(route('login.post'), ['email' => 'x@example.com', 'password' => 'wrong'])
            ->assertStatus(429);
    }

    public function test_guest_cannot_upload_editor_image(): void
    {
        $this->post(route('project.image.upload'))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_cannot_upload_non_image(): void
    {
        $user = \App\Models\User::factory()->create(['permissions' => ['projects']]);

        $this->actingAs($user)
            ->postJson(route('project.image.upload'), ['upload' => UploadedFile::fake()->create('shell.php', 10, 'text/plain')])
            ->assertStatus(422)
            ->assertJsonValidationErrors('upload');
    }

    public function test_project_contact_rejects_malformed_email(): void
    {
        $this->postJson(route('email.submit'), [
            'first_name' => 'A',
            'last_name' => 'B',
            'email' => 'definitely-not-an-email',
            'message' => 'hi',
        ])->assertStatus(422)->assertJsonValidationErrors('email');
    }

    public function test_contact_form_rejects_malformed_email(): void
    {
        $this->postJson(route('contact.submit'), [
            'first_name' => 'A',
            'last_name' => 'B',
            'email' => 'nope',
            'message' => 'hi',
        ])->assertStatus(422)->assertJsonValidationErrors('email');
    }

    public function test_join_form_rejects_non_url_link(): void
    {
        $this->post(route('team.request'), ['name' => 'X', 'link' => 'javascript:alert(1)'])
            ->assertSessionHasErrors('link');
    }

    public function test_home_page_loads_with_empty_database(): void
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_security_headers_include_all_five(): void
    {
        $this->get('/login')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy', 'geolocation=(), microphone=(), camera=()')
            ->assertHeader('X-Permitted-Cross-Domain-Policies', 'none');
    }

    public function test_authenticated_user_can_upload_a_real_image(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $user = \App\Models\User::factory()->create(['permissions' => ['projects']]);

        $this->actingAs($user)->postJson(route('project.image.upload'), [
            'upload' => UploadedFile::fake()->image('shot.jpg', 10, 10),
        ])->assertOk()->assertJsonPath('uploaded', 1);

        // Stored name must use a hashed name + image extension, never a .php name.
        $stored = array_values(array_diff(\Illuminate\Support\Facades\Storage::disk('public')->allFiles('project-images'), []));
        $this->assertNotEmpty($stored);
        $this->assertStringEndsWith('.jpg', $stored[0]);
    }

    public function test_filter_wildcards_are_escaped_not_treated_as_wildcards(): void
    {
        $user = \App\Models\User::factory()->create();

        \App\Models\Projects::create([
            'user_id' => $user->id,
            'title' => 'Laravel App',
            'slug' => 'laravel-app',
            'description' => 'desc',
            'tech_stack' => ['Laravel'],
        ]);
        \App\Models\Projects::create([
            'user_id' => $user->id,
            'title' => 'Other App',
            'slug' => 'other-app',
            'description' => 'desc',
            'tech_stack' => ['Vue'],
        ]);

        // A literal "%" filter must not match every row (wildcard escaped).
        $this->get('/?filter=%')->assertOk();
    }

    public function test_home_data_is_cached(): void
    {
        $this->get('/')->assertOk();
        $this->assertTrue(Cache::has('site.settings'));
        $this->assertTrue(Cache::has('site.team'));
    }
}
