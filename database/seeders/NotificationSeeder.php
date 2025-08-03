<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $notificationService = new NotificationService();
        
        // Get the first admin/manager user for testing
        $adminUser = User::whereIn('role', ['admin', 'manager'])->first();
        
        if (!$adminUser) {
            $this->command->warn('No admin or manager user found. Creating sample notifications for first user.');
            $adminUser = User::first();
        }
        
        if (!$adminUser) {
            $this->command->error('No users found in database. Please create users first.');
            return;
        }

        $this->command->info('Creating sample notifications for user: ' . $adminUser->name);

        // Create sample notifications
        $notifications = [
            [
                'type' => 'payment',
                'title' => 'Payment Received',
                'message' => 'Payment of $1,200 received from Apartment 101',
                'data' => ['amount' => 1200, 'apartment_number' => '101'],
                'icon' => 'dollar',
                'color' => 'blue',
                'created_at' => now()->subMinutes(2),
            ],
            [
                'type' => 'maintenance',
                'title' => 'Maintenance Request',
                'message' => 'New maintenance request from Apartment 205 - Plumbing issue',
                'data' => ['apartment_number' => '205', 'issue' => 'Plumbing'],
                'icon' => 'warning',
                'color' => 'amber',
                'created_at' => now()->subMinutes(15),
            ],
            [
                'type' => 'lease',
                'title' => 'Lease Agreement Signed',
                'message' => 'New lease agreement signed for Apartment 302',
                'data' => ['apartment_number' => '302'],
                'icon' => 'check',
                'color' => 'green',
                'created_at' => now()->subHour(),
                'read_at' => now()->subMinutes(30), // This one is read
            ],
            [
                'type' => 'overdue',
                'title' => 'Overdue Payment Alert',
                'message' => 'Rent payment overdue for Apartment 404 - 5 days late',
                'data' => ['apartment_number' => '404', 'days_overdue' => 5],
                'icon' => 'alert',
                'color' => 'red',
                'created_at' => now()->subHours(2),
            ],
            [
                'type' => 'user_activity',
                'title' => 'New Tenant Registration',
                'message' => 'New tenant John Doe registered for Apartment 508',
                'data' => ['tenant_name' => 'John Doe', 'apartment_number' => '508'],
                'icon' => 'user',
                'color' => 'purple',
                'created_at' => now()->subHours(3),
                'read_at' => now()->subHours(2), // This one is read
            ],
            [
                'type' => 'system',
                'title' => 'System Backup Complete',
                'message' => 'Daily system backup completed successfully',
                'data' => ['backup_size' => '2.3 GB'],
                'icon' => 'info',
                'color' => 'gray',
                'created_at' => now()->subHours(6),
            ],
            [
                'type' => 'payment',
                'title' => 'Payment Failed',
                'message' => 'Payment attempt failed for Apartment 305 - Insufficient funds',
                'data' => ['apartment_number' => '305', 'reason' => 'Insufficient funds'],
                'icon' => 'alert',
                'color' => 'red',
                'created_at' => now()->subHours(8),
            ],
        ];

        foreach ($notifications as $notificationData) {
            Notification::create([
                'user_id' => $adminUser->id,
                ...$notificationData
            ]);
        }

        $this->command->info('Created ' . count($notifications) . ' sample notifications');
        
        // Also create some notifications for tenant users if they exist
        $tenantUsers = User::where('role', 'tenant')->take(2)->get();
        
        foreach ($tenantUsers as $tenant) {
            Notification::create([
                'user_id' => $tenant->id,
                'type' => 'system',
                'title' => 'Welcome to Altezza',
                'message' => 'Welcome to the Altezza Property Management System',
                'data' => null,
                'icon' => 'info',
                'color' => 'blue',
                'created_at' => now()->subDays(1),
            ]);
            
            Notification::create([
                'user_id' => $tenant->id,
                'type' => 'lease',
                'title' => 'Rent Due Reminder',
                'message' => 'Your rent payment is due in 3 days',
                'data' => ['days_until_due' => 3],
                'icon' => 'warning',
                'color' => 'amber',
                'created_at' => now()->subHours(12),
            ]);
        }
        
        if ($tenantUsers->count() > 0) {
            $this->command->info('Created welcome notifications for ' . $tenantUsers->count() . ' tenant users');
        }
    }
}
