<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $dosen = Role::firstOrCreate(['name' => 'dosen']);
        $mahasiswa = Role::firstOrCreate(['name' => 'mahasiswa']);

        // **PASTIKAN** super_admin punya semua permissions
        $allPermissions = Permission::all();
        if ($allPermissions->count() > 0) {
            $superAdmin->syncPermissions($allPermissions);
            $this->command->info("Assigned {$allPermissions->count()} permissions to super_admin");
        } else {
            $this->command->error('No permissions found! Run shield:generate first.');
        }

        // Assign super_admin role to admin@example.com
        $adminUser = User::where('email', 'admin@example.com')->first();
        
        if ($adminUser) {
            $adminUser->syncRoles(['super_admin']);
            $this->command->info('Role super_admin assigned to admin@example.com');
        } else {
            $this->command->error('User with email admin@example.com not found!');
        }
    }
}