<?php

namespace Database\Seeders;

use App\Models\Position;
use App\Models\User;
use App\Models\Website_settings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Idempotent bootstrap: creates the real Super Admin account and the
     * example position presets. Existing accounts are never overwritten
     * (password included) — firstOrCreate semantics everywhere.
     */
    public function run(): void
    {
        $positions = [
            'Developer' => ['projects', 'email_list', 'todo'],
            'Designer' => ['projects', 'email_list', 'todo'],
            'Digital Marketer' => ['projects', 'blog', 'email_list', 'todo'],
            'Super Admin' => User::SECTIONS,
        ];

        foreach ($positions as $name => $permissions) {
            Position::firstOrCreate(
                ['name' => $name],
                ['permissions' => $permissions]
            );
        }

        $superAdminPosition = Position::where('name', 'Super Admin')->first();

        $favIcon = Website_settings::first()?->fav_icon;

        User::firstOrCreate(
            ['email' => 'devhomeadmin@gmail.com'],
            [
                'name' => 'DevHome',
                'password' => Hash::make('DevhOme@sp01'),
                'role' => 'super_admin',
                'position_id' => $superAdminPosition->id,
                'permissions' => User::SECTIONS,
                'designation' => 'Super Admin',
                'slug' => 'devhome-admin',
                'image' => $favIcon,
            ]
        );
    }
}
