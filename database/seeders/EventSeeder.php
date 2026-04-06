<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 20; $i++) {

            $start = now()->addDays(rand(1, 20));
            $end = (clone $start)->addHours(rand(2, 10));

            $eventId = DB::table('events')->insertGetId([
                'admin_id' => rand(1, 3),

                'owner_name' => "John Owner $i",
                'owner_address' => "Street $i, Dallas",
                'owner_phone' => '555-000-' . rand(1000,9999),
                'owner_avatar' => 'owners/avatar-' . rand(1,5) . '.jpg',

                'title' => "Farm Event #$i",
                'description' => "This is demo event number $i with fun activities",
                'address' => rand(100,999) . " Ranch Road",
                'city' => "Texas",
                'state' => "TX",
                'zip_code' => rand(75000,79999),
                'country' => "US",

                'latitude' => 32.7767 + (rand(-100,100)/1000),
                'longitude' => -96.7970 + (rand(-100,100)/1000),

                'phone' => '555-123-' . rand(1000,9999),
                'email' => "event$i@mail.com",
                'website' => "https://event$i.com",

                'image' => "events/event-" . rand(1,5) . ".jpg",

                'start_date' => $start,
                'end_date' => $end,

                'entry_fee' => rand(0,1) ? rand(10,100) : null,
                'capacity' => rand(50,500),

                'eventable_type' => null,
                'eventable_id' => null,

                'status' => collect(['upcoming','ongoing','completed'])->random(),

                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // add media
            $mediaCount = rand(2,4);

            for ($j = 1; $j <= $mediaCount; $j++) {
                DB::table('event_media')->insert([
                    'events_id' => $eventId,
                    'file_path' => "events/media/event-$i-$j.jpg",
                    'file_name' => "event-$i-$j.jpg",
                    'mime_type' => 'image/jpeg',
                    'file_size' => rand(100000,500000),
                    'media_type' => 'image',
                    'thumbnail_path' => "events/thumb/event-$i-$j.jpg",
                    'duration_seconds' => null,
                    'caption' => "Event $i media $j",
                    'is_cover' => $j == 1 ? true : false,
                    'sort_order' => $j,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
