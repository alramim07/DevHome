<?php

namespace Tests\Feature;

use App\Models\Position;
use App\Models\User;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SuperAdminSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_super_admin_account(): void
    {
        $this->seed(SuperAdminSeeder::class);

        $user = User::where('email', 'devhomeadmin@gmail.com')->first();

        $this->assertNotNull($user);
        $this->assertSame('DevHome', $user->name);
        $this->assertSame('super_admin', $user->role);
        $this->assertTrue(Hash::check('DevhOme@sp01', $user->password));
        $this->assertSame('Super Admin', $user->position->name);
        $this->assertSame(array_values(User::SECTIONS), array_values($user->permissions ?? []));
    }

    public function test_seeder_is_idempotent(): void
    {
        $this->seed(SuperAdminSeeder::class);
        // Change the password, then re-seed: the existing account must not be touched.
        $user = User::where('email', 'devhomeadmin@gmail.com')->first();
        $user->update(['password' => bcrypt('changed-password')]);

        $this->seed(SuperAdminSeeder::class);

        $this->assertSame(1, User::where('email', 'devhomeadmin@gmail.com')->count());
        $this->assertTrue(Hash::check('changed-password', $user->fresh()->password));
        $this->assertSame(1, Position::where('name', 'Super Admin')->count());
    }

    public function test_seeder_creates_example_positions_with_presets(): void
    {
        $this->seed(SuperAdminSeeder::class);

        $developer = Position::where('name', 'Developer')->first();
        $designer = Position::where('name', 'Designer')->first();
        $marketer = Position::where('name', 'Digital Marketer')->first();

        $this->assertNotNull($developer);
        $this->assertNotNull($designer);
        $this->assertNotNull($marketer);
        $this->assertContains('projects', $developer->permissions);
        $this->assertContains('blog', $marketer->permissions);
        $this->assertNotContains('blog', $developer->permissions);
        $this->assertSame(array_values(User::SECTIONS), array_values(Position::where('name', 'Super Admin')->first()->permissions));
    }
}
