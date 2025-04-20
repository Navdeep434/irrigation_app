<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // User Management
            'Can Create User',
            'Can Edit User',
            'Can View User',
            'Can Delete User',
            'Can View UserList',

            // Role Management
            'Can Create Role',
            'Can Edit Role',
            'Can View Role',
            'Can Delete Role',
            'Can View RoleList',

            // Permission Management
            'Can Create Permission',
            'Can Edit Permission',
            'Can View Permission',
            'Can Delete Permission',
            'Can View PermissionList',

            // Assign Permissions
            'Can Assign Permissions',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']);
        }

        // Assign all permissions to Superadmin role
        $superAdminRole = Role::where('name', 'superadmin')->first();
        if ($superAdminRole) {
            $superAdminRole->syncPermissions(Permission::all());
        }
    }

}
