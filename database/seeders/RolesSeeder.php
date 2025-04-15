<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Roles with the correct guard
        Role::create(['name' => 'superadmin', 'guard_name' => 'admin']);
        Role::create(['name' => 'admin', 'guard_name' => 'admin']);
        Role::create(['name' => 'technician', 'guard_name' => 'admin']);
        Role::create(['name' => 'user', 'guard_name' => 'web']);
    }
}
