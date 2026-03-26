<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Farm;
use App\Models\FarmMedia;
use App\Models\User;

class FarmSeeder extends Seeder
{
    public function run(): void
    {
        $admins = User::pluck('id')->toArray();

        if (empty($admins)) {
            $this->command->error('No users found. Please seed users first.');
            return;
        }

        $farmNames = [
            'Green Valley Farm',
            'Sunny Acres',
            'Golden Harvest Farm',
            'Blue Sky Farm',
            'Happy Cow Ranch',
            'Riverdale Farm',
            'Fresh Roots Farm',
            'Morning Dew Farm',
            'Evergreen Fields',
            'Nature Fresh Farm',
            'Oakwood Farm',
            'Silver Lake Farm',
            'Red Barn Farm',
            'Green Leaf Farm',
            'Hilltop Farm',
            'Golden Field Farm',
            'Sunrise Farm',
            'Harvest Moon Farm',
            'Meadow View Farm',
            'Country Side Farm'
        ];

        foreach ($farmNames as $index => $name) {

            $farm = Farm::create([
                'admin_id' => $admins[array_rand($admins)],
                'owner_name' => fake()->name(),
                'owner_address' => fake()->address(),
                'owner_phone' => fake()->phoneNumber(),
                'owner_avatar' => 'owners/avatar-' . rand(1, 5) . '.jpg',

                'name' => $name,
                'description' => fake()->paragraph(),
                'address' => fake()->streetAddress(),
                'city' => fake()->city(),
                'state' => fake()->state(),
                'zip_code' => fake()->postcode(),
                'country' => 'US',

                'latitude' => fake()->latitude(25, 49),
                'longitude' => fake()->longitude(-124, -66),

                'phone' => fake()->phoneNumber(),
                'email' => fake()->safeEmail(),
                'website' => fake()->url(),

                'thumbnail' => 'farms/thumb-' . rand(1, 10) . '.jpg',

                'tags' => json_encode([
                    'organic',
                    'vegetables',
                    'dairy',
                    'fruits'
                ]),

                'status' => collect(['active','inactive','pending'])->random(),
                'is_featured' => rand(0,1),

                'marker_color' => fake()->hexColor(),
                'marker_icon' => 'map-marker-' . rand(1,5) . '.png'
            ]);

            // create 3 media per farm
            for ($i = 1; $i <= rand(2,5); $i++) {

                FarmMedia::create([
                    'farm_id' => $farm->id,
                    'file_path' => 'farms/media/farm-'.$farm->id.'-'.$i.'.jpg',
                    'file_name' => 'farm-'.$farm->id.'-'.$i.'.jpg',
                    'mime_type' => 'image/jpeg',
                    'file_size' => rand(200000, 500000),
                    'media_type' => 'image',
                    'thumbnail_path' => 'farms/media/thumb-'.$farm->id.'-'.$i.'.jpg',
                    'caption' => fake()->sentence(),
                    'is_cover' => $i == 1 ? true : false,
                    'sort_order' => $i
                ]);
            }
        }
    }
}
