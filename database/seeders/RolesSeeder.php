<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = Role::firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'admin'
        ]);

        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'admin'
        ]);

        Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'api'
        ]);

        $superAdmin->syncPermissions(Permission::all());

        $admin->syncPermissions([
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage users',
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'manage roles',
            'view permissions'
        ]);
    }
}
