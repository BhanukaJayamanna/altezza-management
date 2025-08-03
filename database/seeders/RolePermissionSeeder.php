<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Owner;
use App\Models\Tenant;
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
            
            // Owner management
            'manage_owners',
            'create_owners',
            'edit_owners',
            'delete_owners',
            'view_owners',
            
            // Apartment management
            'manage_apartments',
            'create_apartments',
            'edit_apartments',
            'delete_apartments',
            'view_apartments',
            
            // Tenant management
            'manage_tenants',
            'create_tenants',
            'edit_tenants',
            'delete_tenants',
            'view_tenants',
            
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
        $tenantRole = Role::firstOrCreate(['name' => 'tenant']);

        // Assign permissions to roles
        $adminRole->syncPermissions($permissions); // Admin gets all permissions

        $managerPermissions = [
            'view_users', 'create_tenants', 'edit_tenants', 'view_tenants',
            'view_owners', 'edit_owners',
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

        $tenantPermissions = [
            'view_apartments', 'view_leases', 'view_invoices', 'view_payments',
            'create_maintenance', 'view_maintenance',
            'create_complaints', 'view_complaints',
            'view_notices',
        ];
        $tenantRole->syncPermissions($tenantPermissions);

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

        // Create sample tenant users
        $tenantUsers = [
            [
                'name' => 'Alice Johnson',
                'email' => 'alice.johnson@email.com',
                'phone' => '+1234567893',
                'apartment_number' => '001',
                'lease_start' => '2024-01-01',
                'lease_end' => '2024-12-31',
                'security_deposit' => 1500,
                'emergency_contact_name' => 'Bob Johnson',
                'emergency_contact_phone' => '+1234567894',
            ],
            [
                'name' => 'David Smith',
                'email' => 'david.smith@email.com',
                'phone' => '+1234567895',
                'apartment_number' => '003',
                'lease_start' => '2024-02-15',
                'lease_end' => '2025-02-14',
                'security_deposit' => 1800,
                'emergency_contact_name' => 'Sarah Smith',
                'emergency_contact_phone' => '+1234567896',
            ],
            [
                'name' => 'Emma Wilson',
                'email' => 'emma.wilson@email.com',
                'phone' => '+1234567897',
                'apartment_number' => '005',
                'lease_start' => '2024-03-01',
                'lease_end' => '2025-02-28',
                'security_deposit' => 2000,
                'emergency_contact_name' => 'James Wilson',
                'emergency_contact_phone' => '+1234567898',
            ],
        ];

        foreach ($tenantUsers as $tenantData) {
            // Create tenant user
            $tenant = User::firstOrCreate(
                ['email' => $tenantData['email']],
                [
                    'name' => $tenantData['name'],
                    'password' => Hash::make('password'),
                    'role' => 'tenant',
                    'status' => 'active',
                    'phone' => $tenantData['phone'],
                ]
            );
            $tenant->assignRole('tenant');

            // Find the apartment
            $apartment = Apartment::where('number', $tenantData['apartment_number'])->first();
            
            if ($apartment) {
                // Create tenant profile
                \App\Models\Tenant::firstOrCreate(
                    ['user_id' => $tenant->id],
                    [
                        'apartment_id' => $apartment->id,
                        'lease_start' => $tenantData['lease_start'],
                        'lease_end' => $tenantData['lease_end'],
                        'emergency_contact' => $tenantData['emergency_contact_name'],
                        'emergency_phone' => $tenantData['emergency_contact_phone'],
                        'status' => 'active',
                    ]
                );

                // Update apartment status and assign tenant
                $apartment->update([
                    'tenant_id' => $tenant->id,
                    'status' => 'occupied'
                ]);
            }
        }

        // Create sample invoices
        $occupiedApartments = Apartment::where('status', 'occupied')->whereNotNull('tenant_id')->get();
        
        foreach ($occupiedApartments as $apartment) {
            if ($apartment->tenant_id && $apartment->rent_amount) {
                // Create a few rent invoices for the past months
                for ($monthsBack = 2; $monthsBack >= 0; $monthsBack--) {
                    $invoiceDate = Carbon::now()->subMonths($monthsBack);
                    $dueDate = $invoiceDate->copy()->addDays(30);
                    
                    $invoiceNumber = sprintf("INV-%s-%04d", 
                        $invoiceDate->format('Ym'), 
                        $apartment->id * 10 + $monthsBack + 1
                    );

                    $status = 'pending';
                    if ($monthsBack == 2) {
                        $status = 'paid'; // Oldest invoice is paid
                    } elseif ($monthsBack == 1 && $dueDate->isPast()) {
                        $status = 'overdue'; // Middle invoice is overdue
                    }

                    \App\Models\Invoice::firstOrCreate([
                        'invoice_number' => $invoiceNumber
                    ], [
                        'apartment_id' => $apartment->id,
                        'tenant_id' => $apartment->tenant_id,
                        'type' => 'rent',
                        'status' => $status,
                        'billing_period_start' => $invoiceDate->startOfMonth(),
                        'billing_period_end' => $invoiceDate->endOfMonth(),
                        'due_date' => $dueDate,
                        'amount' => $apartment->rent_amount,
                        'total_amount' => $apartment->rent_amount,
                        'description' => "Monthly rent for {$apartment->number} - " . $invoiceDate->format('F Y'),
                        'created_by' => $admin->id,
                        'created_at' => $invoiceDate,
                        'paid_on' => $status === 'paid' ? $dueDate->subDays(5) : null,
                    ]);
                }
            }
        }

        // Create a sample owner
        $owner = Owner::firstOrCreate(
            ['email' => 'owner@altezza.com'],
            [
                'name' => 'John Property Owner',
                'phone' => '+1234567892',
                'address' => '123 Owner Street, City',
                'id_document' => 'ID123456789',
                'bank_details' => [
                    'bank_name' => 'Sample Bank',
                    'account_number' => '1234567890',
                    'account_holder' => 'John Property Owner',
                    'routing_number' => '123456789'
                ],
                'status' => 'active',
            ]
        );

        // Create sample apartments
        $apartmentTypes = ['1bhk', '2bhk', '3bhk'];
        $blocks = ['A', 'B', 'C'];

        for ($i = 1; $i <= 15; $i++) {
            Apartment::firstOrCreate(
                ['number' => sprintf('%03d', $i)],
                [
                    'block' => $blocks[array_rand($blocks)],
                    'floor' => ceil($i / 5),
                    'type' => $apartmentTypes[array_rand($apartmentTypes)],
                    'status' => 'vacant',
                    'area' => rand(500, 1500),
                    'rent_amount' => rand(800, 2500),
                    'description' => "Apartment {$i} - Well maintained unit",
                    'owner_id' => $owner->id,
                ]
            );
        }

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

        $this->command->info('Roles, permissions, and sample data created successfully!');
        $this->command->info('Admin login: admin@altezza.com / password');
        $this->command->info('Manager login: manager@altezza.com / password');
    }
}
