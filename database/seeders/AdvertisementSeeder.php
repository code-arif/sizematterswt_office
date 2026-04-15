<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdvertisementSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 20; $i++) {

            $advertiseId = DB::table('advertises')->insertGetId([
                'user_id' => rand(1, 5),
                'advertiser' => rand(0, 1) ? 'admin' : 'user',

                'advertiseable_type' => null,
                'advertiseable_id' => null,

                'title' => "Special Offer #$i",
                'subtitle' => "Limited time deal #$i",
                'image' => 'ads/sample-' . rand(1,5) . '.jpg',
                'cta_label' => 'View Details',

                'trigger_latitude' => 23.8103 + (rand(-100, 100) / 1000),
                'trigger_longitude' => 90.4125 + (rand(-100, 100) / 1000),
                'radius_meters' => rand(500, 3000),

                'status' => 'active',
                'starts_at' => now()->subDays(rand(1,5)),
                'ends_at' => now()->addDays(rand(5,20)),

                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $users = collect(range(1,10))->shuffle()->take(rand(1,5));
            $deviceId = Str::uuid();

            foreach ($users as $userId) {
                DB::table('ad_impressions')->insert([
                    'advertise_id' => $advertiseId,
                    'user_id' => $userId,
                    'device_id' => $deviceId,
                    'is_dismissed' => rand(0,1),
                    'seen_at' => now()->subMinutes(rand(1,500)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
