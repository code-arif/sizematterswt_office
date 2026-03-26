<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ranche;
use App\Models\RanchMedia;
use App\Models\User;

class RanchSeeder extends Seeder
{
    public function run(): void
    {
        $admins = User::pluck('id')->toArray();

        if (empty($admins)) {
            $this->command->error('No users found. Seed users first.');
            return;
        }

        $ranchNames = [
            'Silver Creek Ranch',
            'Red Rock Ranch',
            'Blue River Ranch',
            'Golden Horse Ranch',
            'Wild West Ranch',
            'Green Hills Ranch',
            'Lazy R Ranch',
            'Sunset Valley Ranch',
            'Big Sky Ranch',
            'Rolling Meadows Ranch',
            'Cedar Wood Ranch',
            'Iron Gate Ranch',
            'Lone Star Ranch',
            'Prairie Wind Ranch',
            'Thunder Ridge Ranch',
            'Broken Arrow Ranch',
            'Highland Ranch',
            'River Bend Ranch',
            'Oak Valley Ranch',
            'Diamond Spur Ranch'
        ];

        foreach ($ranchNames as $name) {

            $ranch = Ranche::create([
                'admin_id' => $admins[array_rand($admins)],

                'owner_name' => fake()->name(),
                'owner_address' => fake()->address(),
                'owner_phone' => fake()->phoneNumber(),
                'owner_avatar' => 'owners/avatar-' . rand(1,5) . '.jpg',

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

                'thumbnail' => 'ranches/thumb-' . rand(1,10) . '.jpg',

                'tags' => json_encode([
                    'horse',
                    'cattle',
                    'cowboy',
                    'rodeo'
                ]),

                'acreage' => rand(50, 5000),

                'status' => collect(['active','inactive','pending'])->random(),
                'is_featured' => rand(0,1),

                'marker_color' => fake()->hexColor(),
                'marker_icon' => 'map-marker-' . rand(1,5) . '.png',
            ]);

            // create media
            for ($i = 1; $i <= rand(2,5); $i++) {

                RanchMedia::create([
                    'ranch_id' => $ranch->id,
                    'file_path' => 'ranches/media/ranch-'.$ranch->id.'-'.$i.'.jpg',
                    'file_name' => 'ranch-'.$ranch->id.'-'.$i.'.jpg',
                    'mime_type' => 'image/jpeg',
                    'file_size' => rand(200000, 900000),
                    'media_type' => 'image',
                    'thumbnail_path' => 'ranches/media/thumb-'.$ranch->id.'-'.$i.'.jpg',
                    'caption' => fake()->sentence(),
                    'is_cover' => $i == 1 ? true : false,
                    'sort_order' => $i
                ]);
            }
        }
    }
}
