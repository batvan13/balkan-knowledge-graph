<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@bkg.local'],
            [
                'name'       => 'BKG Admin',
                'email'      => 'admin@bkg.local',
                'password'   => Hash::make('admin1234'),
                'is_admin'   => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
