<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Roles & Permissions
        $this->call(RolesAndPermissionsSeeder::class);

        // 2. Seed System Settings
        $this->call(SettingSeeder::class);

        // 3. Create Default Admin User
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@speechiq.com'],
            [
                'name' => 'SpeechIQ Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]
        );
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            $adminUser->roles()->sync([$adminRole->id]);
        }

        // 4. Create Default Candidate User
        $candidateUser = User::updateOrCreate(
            ['email' => 'candidate@speechiq.com'],
            [
                'name' => 'Candidate User',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]
        );
        $userRole = Role::where('slug', 'user')->first();
        if ($userRole) {
            $candidateUser->roles()->sync([$userRole->id]);
        }

        // 5. Seed Read Aloud Tests
        $this->call(ReadAloudTestSeeder::class);

        // 6. Seed AI Interview Tests
        $this->call(AiInterviewTestSeeder::class);
    }
}
