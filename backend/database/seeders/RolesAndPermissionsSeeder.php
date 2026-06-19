<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define default permissions
        $permissions = [
            ['name' => 'View Dashboard', 'slug' => 'view-dashboard', 'description' => 'Allow viewing candidate/admin dashboard'],
            ['name' => 'Manage Settings', 'slug' => 'manage-settings', 'description' => 'Allow managing app settings and API keys'],
            ['name' => 'Manage Tests', 'slug' => 'manage-tests', 'description' => 'Allow creating and updating tests'],
            ['name' => 'Manage Users', 'slug' => 'manage-users', 'description' => 'Allow view/edit users'],
        ];

        foreach ($permissions as $p) {
            Permission::updateOrCreate(['slug' => $p['slug']], $p);
        }

        // Define default roles
        $adminRole = Role::updateOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Administrator', 'description' => 'System administrator with full permissions']
        );

        $userRole = Role::updateOrCreate(
            ['slug' => 'user'],
            ['name' => 'User', 'description' => 'General candidate user with access to practice and test modules']
        );

        // Assign all permissions to admin
        $allPermissions = Permission::all();
        $adminRole->permissions()->sync($allPermissions->pluck('id'));

        // Assign dashboard permissions to user
        $dashboardPermission = Permission::where('slug', 'view-dashboard')->first();
        if ($dashboardPermission) {
            $userRole->permissions()->sync([$dashboardPermission->id]);
        }
    }
}
