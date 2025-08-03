<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PaymentPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create payment permissions
        Permission::firstOrCreate(['name' => 'manage_payments']);
        Permission::firstOrCreate(['name' => 'view_payments']);

        // Get roles
        $adminRole = Role::findByName('admin');
        $managerRole = Role::findByName('manager');

        // Grant payment permissions to admin and manager
        $adminRole->givePermissionTo(['manage_payments', 'view_payments']);
        $managerRole->givePermissionTo(['manage_payments', 'view_payments']);

        $this->command->info('Payment permissions created and assigned successfully.');
    }
}
