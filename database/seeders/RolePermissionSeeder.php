<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\ManagementCorporation;
use App\Models\Owner;
use App\Models\Apartment;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // User management
            'manage_users',
            'create_users',
            'edit_users',
            'delete_users',
            'view_users',
            
            // Management Corporation management
            'manage_management_corporations',
            'create_management_corporations',
            'edit_management_corporations',
            'delete_management_corporations',
            'view_management_corporations',
            
            // Apartment management
            'manage_apartments',
            'create_apartments',
            'edit_apartments',
            'delete_apartments',
            'view_apartments',
            
            // Owner management
            'manage_owners',
            'create_owners',
            'edit_owners',
            'delete_owners',
            'view_owners',
            
            // Lease management
            'manage_leases',
            'create_leases',
            'edit_leases',
            'delete_leases',
            'view_leases',
            
            // Invoice management
            'manage_invoices',
            'create_invoices',
            'edit_invoices',
            'delete_invoices',
            'view_invoices',
            
            // Payment management
            'manage_payments',
            'create_payments',
            'edit_payments',
            'delete_payments',
            'view_payments',
            
            // Maintenance management
            'manage_maintenance',
            'create_maintenance',
            'edit_maintenance',
            'delete_maintenance',
            'view_maintenance',
            'assign_maintenance',
            
            // Complaint management
            'manage_complaints',
            'create_complaints',
            'edit_complaints',
            'delete_complaints',
            'view_complaints',
            'assign_complaints',
            
            // Notice management
            'manage_notices',
            'create_notices',
            'edit_notices',
            'delete_notices',
            'view_notices',
            
            // Utility management
            'manage_utilities',
            'create_utilities',
            'edit_utilities',
            'delete_utilities',
            'view_utilities',
            
            // Reporting
            'view_reports',
            'export_reports',
            
            // Settings
            'manage_settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $ownerRole = Role::firstOrCreate(['name' => 'owner']);

        // Assign permissions to roles
        $adminRole->syncPermissions($permissions); // Admin gets all permissions

        $managerPermissions = [
            'view_users', 'create_owners', 'edit_owners', 'view_owners',
            'view_management_corporations', 'edit_management_corporations',
            'manage_apartments', 'create_apartments', 'edit_apartments', 'view_apartments',
            'manage_leases', 'create_leases', 'edit_leases', 'view_leases',
            'manage_invoices', 'create_invoices', 'edit_invoices', 'view_invoices',
            'manage_payments', 'create_payments', 'edit_payments', 'view_payments',
            'manage_maintenance', 'edit_maintenance', 'view_maintenance', 'assign_maintenance',
            'manage_complaints', 'edit_complaints', 'view_complaints', 'assign_complaints',
            'manage_notices', 'create_notices', 'edit_notices', 'view_notices',
            'manage_utilities', 'create_utilities', 'edit_utilities', 'view_utilities',
            'view_reports', 'export_reports',
        ];
        $managerRole->syncPermissions($managerPermissions);

        $ownerPermissions = [
            'view_apartments', 'view_leases', 'view_invoices', 'view_payments',
            'create_maintenance', 'view_maintenance',
            'create_complaints', 'view_complaints',
            'view_notices',
        ];
        $ownerRole->syncPermissions($ownerPermissions);

        // Create default admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@altezza.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
                'phone' => '+1234567890',
            ]
        );
        $admin->assignRole('admin');

        // Create default manager user
        $manager = User::firstOrCreate(
            ['email' => 'manager@altezza.com'],
            [
                'name' => 'Property Manager',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'status' => 'active',
                'phone' => '+1234567891',
            ]
        );
        $manager->assignRole('manager');

        // Create system settings
        $settings = [
            ['key' => 'company_name', 'value' => 'Altezza Property Management', 'group' => 'general'],
            ['key' => 'company_address', 'value' => '123 Business Street, City, State 12345', 'group' => 'general'],
            ['key' => 'company_phone', 'value' => '+1 (555) 123-4567', 'group' => 'general'],
            ['key' => 'company_email', 'value' => 'info@altezza.com', 'group' => 'general'],
            ['key' => 'late_fee_percentage', 'value' => '5', 'type' => 'integer', 'group' => 'billing'],
            ['key' => 'late_fee_grace_days', 'value' => '5', 'type' => 'integer', 'group' => 'billing'],
            ['key' => 'invoice_due_days', 'value' => '30', 'type' => 'integer', 'group' => 'billing'],
            ['key' => 'reminder_days_before_due', 'value' => '7', 'type' => 'integer', 'group' => 'notifications'],
            ['key' => 'renewal_reminder_days', 'value' => '60', 'type' => 'integer', 'group' => 'notifications'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Roles, permissions, and system settings created successfully!');
        $this->command->info('Admin login: admin@altezza.com / password');
        $this->command->info('Manager login: manager@altezza.com / password');
    }
}
