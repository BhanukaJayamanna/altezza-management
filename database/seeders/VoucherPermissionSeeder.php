<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class VoucherPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create voucher permissions
        Permission::firstOrCreate(['name' => 'manage_vouchers']);
        Permission::firstOrCreate(['name' => 'view_vouchers']);
        Permission::firstOrCreate(['name' => 'approve_vouchers']);
        Permission::firstOrCreate(['name' => 'create_vouchers']);
        Permission::firstOrCreate(['name' => 'edit_vouchers']);
        Permission::firstOrCreate(['name' => 'delete_vouchers']);

        // Get roles
        $adminRole = Role::findByName('admin');
        $managerRole = Role::findByName('manager');

        // Grant all voucher permissions to admin
        $adminRole->givePermissionTo([
            'manage_vouchers',
            'view_vouchers',
            'approve_vouchers',
            'create_vouchers',
            'edit_vouchers',
            'delete_vouchers'
        ]);

        // Grant voucher permissions to manager (except delete)
        $managerRole->givePermissionTo([
            'manage_vouchers',
            'view_vouchers',
            'approve_vouchers',
            'create_vouchers',
            'edit_vouchers'
        ]);

        $this->command->info('Voucher permissions created and assigned successfully.');
    }
}
