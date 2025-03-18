<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'super_admin',
                'description' => 'Full system access'
            ],
            [
                'name' => 'admin',
                'description' => 'Manages websites and agents'
            ],
            [
                'name' => 'agent', 
                'description' => 'Handles customer chats'
            ]
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                [
                    'guard_name' => 'web',
                    'description' => $role['description']
                ]
            );
        }
    }
}