<?php

namespace Database\Seeders;

use App\Helpers\FileHandle;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('name', 'super-admin')->where('guard_name', 'admin')->first();
        $adminRole = Role::where('name', 'admin')->where('guard_name', 'admin')->first();
        $userRole = Role::where('name', 'user')->where('guard_name', 'api')->first();

        $superAdmin = User::create([
            'email' => 'superadmin@example.com',
            'phone' => '01710000000',
            'password' => Hash::make('12345678'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $superAdmin->assignRole($superAdminRole);

        if (!$superAdmin->profile) {
            $superAdmin->profile()->create([
                'name' => 'Super Admin',
                'username' => FileHandle::generateUsername('Super'),
                'slug' => FileHandle::generateSlug('Super'),
            ]);
        }

        for ($i = 1; $i <= 2; $i++) {

            $admin = User::create([
                'email' => "admin{$i}@gmail.com",
                'phone' => "0171000000{$i}",
                'password' => Hash::make('12345678'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            $admin->assignRole($adminRole);
        }

        for ($i = 1; $i <= 10; $i++) {

            $user = User::create([
                'email' => "user{$i}@gmail.com",
                'phone' => "0181000000{$i}",
                'password' => Hash::make('12345678'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            $user->assignRole($userRole);
        }
    }
}
