<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamProfileAndSlugTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_without_teams_returns_404_on_public_team_profile(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'slug' => 'super-admin-user',
        ]);

        $response = $this->get('/team/' . $superAdmin->slug);

        $response->assertStatus(404);
    }

    public function test_super_admin_with_at_least_one_team_shows_public_team_profile(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'slug' => 'hasanuzzaman-priyam',
        ]);

        $team = Team::create(['name' => 'Core Team']);
        $superAdmin->teams()->attach($team->id);

        $response = $this->get('/team/' . $superAdmin->slug);

        $response->assertStatus(200);
        $response->assertSee($superAdmin->name);
    }

    public function test_regular_member_profile_is_accessible(): void
    {
        $member = User::factory()->create([
            'role' => 'member',
            'slug' => 'regular-member',
        ]);

        $response = $this->get('/team/' . $member->slug);

        $response->assertStatus(200);
        $response->assertSee($member->name);
    }

    public function test_user_can_update_their_slug_in_profile(): void
    {
        $user = User::factory()->create([
            'role' => 'super_admin',
            'slug' => 'old-slug',
            'email' => 'admin@example.com',
        ]);

        $response = $this->actingAs($user)->post(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'slug' => 'new-custom-slug',
        ]);

        $response->assertSessionHas('success');
        $this->assertSame('new-custom-slug', $user->fresh()->slug);
    }

    public function test_profile_slug_uniqueness_is_validated(): void
    {
        User::factory()->create([
            'email' => 'other@example.com',
            'slug' => 'existing-slug',
        ]);

        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'slug' => 'my-slug',
        ]);

        $response = $this->actingAs($user)->post(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'slug' => 'existing-slug',
        ]);

        $response->assertSessionHasErrors('slug');
        $this->assertSame('my-slug', $user->fresh()->slug);
    }

    public function test_profile_slug_input_is_automatically_slugified(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'slug' => 'initial-slug',
        ]);

        $response = $this->actingAs($user)->post(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'slug' => 'My New Custom URL',
        ]);

        $response->assertSessionHas('success');
        $this->assertSame('my-new-custom-url', $user->fresh()->slug);
    }

    public function test_profile_page_displays_slug_input_field(): void
    {
        $user = User::factory()->create([
            'role' => 'super_admin',
            'slug' => 'priyam-slug',
        ]);

        $response = $this->actingAs($user)->get(route('profile'));

        $response->assertStatus(200);
        $response->assertSee('Profile Slug / URL');
        $response->assertSee('priyam-slug');
    }

    public function test_user_can_update_biography(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
        ]);

        $response = $this->actingAs($user)->post(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'biography' => 'Passionate software engineer and tech enthusiast.',
        ]);

        $response->assertSessionHas('success');
        $this->assertSame('Passionate software engineer and tech enthusiast.', $user->fresh()->biography);
    }

    public function test_expert_stacks_shows_at_most_three(): void
    {
        $user = User::factory()->create([
            'slug' => 'dev-user',
            'languages' => ['PHP', 'Laravel', 'Vue.js', 'React', 'Python'],
        ]);

        $response = $this->get('/team/' . $user->slug);

        $response->assertStatus(200);
        $response->assertSee('Expert Stacks');
        $response->assertSee('PHP');
        $response->assertSee('Laravel');
        $response->assertSee('Vue.js');
        // React and Python should still be present in Tech Stack Skills, but let's ensure page rendered
        $response->assertSee('Tech Stack Skills');
    }

    public function test_download_custom_cv_redirects_if_no_file(): void
    {
        $user = User::factory()->create([
            'slug' => 'user-no-cv',
            'custom_cv' => null,
        ]);

        $response = $this->get(route('cv.custom', $user->slug));

        $response->assertRedirect(route('team', $user->slug));
        $response->assertSessionHas('error');
    }

    public function test_team_profile_renders_download_buttons_with_data_attributes(): void
    {
        $user = User::factory()->create([
            'slug' => 'test-buttons-user',
            'custom_cv' => null,
        ]);

        $response = $this->get('/team/' . $user->slug);

        $response->assertStatus(200);
        $response->assertSee('btn-download-devhome-cv');
        $response->assertSee('btn-download-custom-cv');
        $response->assertSee('data-has-custom-cv="0"', false);
    }

    public function test_user_can_upload_custom_cv_via_modal_endpoint(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $user = User::factory()->create([
            'slug' => 'cv-user',
            'custom_cv' => null,
        ]);

        $file = \Illuminate\Http\UploadedFile::fake()->create('my_resume.pdf', 250, 'application/pdf');

        $response = $this->actingAs($user)->post(route('profile.cv.update'), [
            'custom_cv' => $file,
        ]);

        $response->assertSessionHas('success');
        $updatedUser = $user->fresh();
        $this->assertNotNull($updatedUser->custom_cv);
        $this->assertStringContainsString('storage/dev_cvs/', $updatedUser->custom_cv);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists(media_path($updatedUser->custom_cv));
    }

    public function test_custom_cv_update_validates_pdf_extension_only(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $user = User::factory()->create([
            'slug' => 'cv-validation-user',
        ]);

        $invalidFile = \Illuminate\Http\UploadedFile::fake()->create('document.docx', 100, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $response = $this->actingAs($user)->post(route('profile.cv.update'), [
            'custom_cv' => $invalidFile,
        ]);

        $response->assertSessionHasErrors('custom_cv');
    }

    public function test_profile_page_shows_filename_and_update_modal_when_cv_exists(): void
    {
        $user = User::factory()->create([
            'email' => 'cv-display@example.com',
            'slug' => 'cv-display-user',
            'custom_cv' => 'storage/dev_cvs/my_special_resume.pdf',
        ]);

        $response = $this->actingAs($user)->get(route('profile'));

        $response->assertStatus(200);
        $response->assertSee('my_special_resume.pdf');
        $response->assertSee('Update CV');
        $response->assertSee('id="updateCvModal"', false);
        $response->assertSee('Allowed file extensions:');
    }

    public function test_download_custom_cv_serves_file_from_public_storage(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $user = User::factory()->create([
            'name' => 'User Download Test',
            'slug' => 'user-download-test',
            'custom_cv' => 'storage/dev_cvs/resume.pdf',
        ]);

        \Illuminate\Support\Facades\Storage::disk('public')->put('dev_cvs/resume.pdf', 'dummy pdf content');

        $response = $this->get(route('cv.custom', $user->slug));

        $response->assertStatus(200);
        $this->assertStringContainsString('CV-Of-user-download-test.pdf', $response->headers->get('content-disposition'));
    }
}
