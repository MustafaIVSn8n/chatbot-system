<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'admin@chatbotsystem.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'), // Change this in production
                'email_verified_at' => now(),
                //'phone_number' => null
            ]
        );

        // Assign role using Spatie's permission system
        $user->assignRole('super_admin');
    }
}