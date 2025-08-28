<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@altezza.com'],
            [
                'name' => 'System Administrator',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'phone' => '+1234567890',
                'status' => 'active',
            ]
        );
        $admin->assignRole('admin');

        // Create Manager User
        $manager = User::firstOrCreate(
            ['email' => 'manager@altezza.com'],
            [
                'name' => 'Property Manager',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'phone' => '+1234567891',
                'status' => 'active',
            ]
        );
        $manager->assignRole('manager');

        $this->command->info('Essential user roles created successfully!');
        $this->command->info('Admin login: admin@altezza.com / password');
        $this->command->info('Manager login: manager@altezza.com / password');
    }
}
