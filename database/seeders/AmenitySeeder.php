<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        $codes = [
            'wifi',
            'parking',
            'restaurant',
            'bar',
            'pool',
            'spa',
            'fitness_center',
            'air_conditioning',
            'heating',
            'breakfast',
            'room_service',
            'airport_transfer',
            'pet_friendly',
            'family_friendly',
            'non_smoking_rooms',
            'facilities_for_disabled_guests',
            'private_bathroom',
            'balcony',
            'kitchen',
            'sea_view',
            'mountain_view',
            'beach_access',
            'garden',
            'terrace',
        ];

        foreach ($codes as $code) {
            DB::table('amenities')->insertOrIgnore([
                'code'       => $code,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
