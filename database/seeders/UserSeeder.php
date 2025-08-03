<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Owner;
use App\Models\Apartment;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@altezza.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'phone' => '+1234567890',
            'role' => 'admin',
            'status' => 'active',
        ]);
        $admin->assignRole('admin');

        // Create Manager User
        $manager = User::create([
            'name' => 'Property Manager',
            'email' => 'manager@altezza.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'phone' => '+1234567891',
            'role' => 'manager',
            'status' => 'active',
        ]);
        $manager->assignRole('manager');

        // Create some tenant users
        $tenantUsers = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@email.com',
                'phone' => '+1234567892',
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@email.com',
                'phone' => '+1234567893',
            ],
            [
                'name' => 'Mike Wilson',
                'email' => 'mike.wilson@email.com',
                'phone' => '+1234567894',
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily.davis@email.com',
                'phone' => '+1234567895',
            ],
        ];

        foreach ($tenantUsers as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'phone' => $userData['phone'],
                'role' => 'tenant',
                'status' => 'active',
            ]);
            $user->assignRole('tenant');

            // Create tenant profile
            Tenant::create([
                'user_id' => $user->id,
                'emergency_contact_name' => 'Emergency Contact for ' . $userData['name'],
                'emergency_contact_phone' => '+1987654321',
                'occupation' => 'Professional',
                'monthly_income' => rand(3000, 8000),
                'id_number' => 'ID' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT),
                'status' => 'active',
            ]);
        }

        $this->command->info('Users created successfully!');
        $this->command->info('Admin: admin@altezza.com / password');
        $this->command->info('Manager: manager@altezza.com / password');
        $this->command->info('Tenants: john.smith@email.com, sarah.johnson@email.com, etc. / password');
    }
}
