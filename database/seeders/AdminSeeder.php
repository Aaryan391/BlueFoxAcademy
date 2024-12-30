<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'phone' => '1234567890',
            'role' => 'admin',
            'address' => 'Admin Address',
            'city' => 'Admin City',
            'state' => 'Admin State',
            'postcode' => '123456',
            'profile_picture' => null, // Optional
            'bio' => 'This is the admin account for the platform.',
            'occupation' => 'Administrator',
            'company_name' => 'EduChamp',
            'linkedin' => null,
            'facebook' => null,
            'twitter' => null,
            'instagram' => null,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
