<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EntityTypeSeeder extends Seeder
{
    public function run(): void
    {
        $codes = [
            // accommodation
            'hotel',
            'guesthouse',
            'apartment',
            'house',
            'villa',
            'hostel',
            'bungalow',
            'camping',
            'lodge',
            // food places
            'restaurant',
            'tavern',
            'bar',
            'pub',
            'cafe',
            'bistro',
            'fast_food',
            'pastry_shop',
            // attractions
            'museum',
            'gallery',
            'monument',
            'monastery',
            'church',
            'chapel',
            'fortress',
            'castle',
            'palace',
            'tomb',
            'megalith',
            'waterfall',
            'cave',
            'beach',
            'park',
            'reservoir',
            'spring',
            'rock_formation',
            'heritage_tree',
            'observatory',
            'planetarium',
            'zoo',
        ];

        foreach ($codes as $code) {
            DB::table('entity_types')->insertOrIgnore([
                'code'       => $code,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
