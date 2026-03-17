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

        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        */

        $superAdmin = User::create([
            'email' => 'superadmin@example.com',
            'phone' => '01710000000',
            'password' => Hash::make('12345678'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $superAdmin->assignRole($superAdminRole);

        $superAdmin->profile()->create([
            'name' => 'Super Admin',
            'username' => FileHandle::generateUsername('superadmin'),
            'slug' => FileHandle::generateSlug('super-admin'),
            'tagline' => 'System Super Administrator',
            'biography' => 'This is the main system administrator.',
            'is_agreed' => true,
            'is_online' => false,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Admin Users
        |--------------------------------------------------------------------------
        */

        for ($i = 1; $i <= 2; $i++) {

            $admin = User::create([
                'email' => "admin{$i}@gmail.com",
                'phone' => "0171000000{$i}",
                'password' => Hash::make('12345678'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            $admin->assignRole($adminRole);

            $admin->profile()->create([
                'name' => "Admin {$i}",
                'username' => FileHandle::generateUsername("admin{$i}"),
                'slug' => FileHandle::generateSlug("admin-{$i}"),
                'tagline' => 'System Administrator',
                'biography' => 'Administrator account for managing the platform.',
                'is_agreed' => true,
                'is_online' => false,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Regular Users
        |--------------------------------------------------------------------------
        */

        for ($i = 1; $i <= 10; $i++) {

            $user = User::create([
                'email' => "user{$i}@gmail.com",
                'phone' => "0181000000{$i}",
                'password' => Hash::make('12345678'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            $user->assignRole($userRole);

            $user->profile()->create([
                'name' => "User {$i}",
                'username' => FileHandle::generateUsername("user{$i}"),
                'slug' => FileHandle::generateSlug("user-{$i}"),
                'tagline' => 'Platform Member',
                'biography' => 'This is a sample user account.',
                'is_agreed' => true,
                'is_online' => false,
            ]);
        }
    }
}
