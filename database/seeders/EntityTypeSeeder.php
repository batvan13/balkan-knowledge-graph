<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EntityTypeSeeder extends Seeder
{
    public function run(): void
    {
        $codes = [
            'hotel',
            'guesthouse',
            'apartment',
            'house',
            'villa',
            'hostel',
            'bungalow',
            'camping',
            'lodge',
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
