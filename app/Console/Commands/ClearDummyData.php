<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Owner;
use App\Models\Apartment;
use App\Models\Tenant;
use App\Models\Lease;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentVoucher;
use App\Models\UtilityBill;
use App\Models\UtilityMeter;
use App\Models\UtilityReading;
use App\Models\UtilityBillPayment;
use App\Models\MaintenanceRequest;
use App\Models\Complaint;
use App\Models\Notice;
use App\Models\Notification;

class ClearDummyData extends Command
{
    protected $signature = 'data:clear-dummy';
    protected $description = 'Clear all dummy data while keeping admin and manager accounts';

    public function handle()
    {
        $this->info('Starting to clear dummy data...');

        // Keep track of what we're removing
        $this->info('Current data counts:');
        $this->line('Users: ' . User::count());
        $this->line('Owners: ' . Owner::count());
        $this->line('Apartments: ' . Apartment::count());
        $this->line('Tenants: ' . Tenant::count());
        $this->line('Leases: ' . Lease::count());

        if ($this->confirm('Do you want to proceed with clearing dummy data? (This will keep only admin@altezza.com and manager@altezza.com)')) {
            
            // Clear all transactional data first
            $this->clearTransactionalData();
            
            // Clear master data (but keep admin/manager users)
            $this->clearMasterData();
            
            $this->info('Dummy data cleared successfully!');
            $this->info('Remaining data:');
            $this->line('Users: ' . User::count());
            $this->line('Owners: ' . Owner::count());
            $this->line('Apartments: ' . Apartment::count());
            $this->line('Tenants: ' . Tenant::count());
            
            $this->info('Admin login: admin@altezza.com / password');
            $this->info('Manager login: manager@altezza.com / password');
        } else {
            $this->info('Operation cancelled.');
        }

        return 0;
    }

    private function clearTransactionalData()
    {
        $this->info('Clearing transactional data...');

        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear notifications
        Notification::truncate();
        $this->line('✓ Notifications cleared');

        // Clear utility data (in correct order due to foreign keys)
        UtilityBillPayment::truncate();
        UtilityBill::truncate();
        UtilityReading::truncate();
        UtilityMeter::truncate();
        $this->line('✓ Utility data cleared');

        // Clear financial data
        Payment::truncate();
        Invoice::truncate();
        PaymentVoucher::truncate();
        $this->line('✓ Financial data cleared');

        // Clear operational data
        MaintenanceRequest::truncate();
        Complaint::truncate();
        Notice::truncate();
        $this->line('✓ Operational data cleared');

        // Clear leases
        Lease::truncate();
        $this->line('✓ Leases cleared');

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function clearMasterData()
    {
        $this->info('Clearing master data...');

        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear tenants
        Tenant::truncate();
        $this->line('✓ Tenants cleared');

        // Clear apartments
        Apartment::truncate();
        $this->line('✓ Apartments cleared');

        // Clear owners
        Owner::truncate();
        $this->line('✓ Owners cleared');

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Clear dummy users (keep only admin and manager)
        $dummyUsers = User::whereNotIn('email', ['admin@altezza.com', 'manager@altezza.com'])->get();
        foreach ($dummyUsers as $user) {
            $this->line('Removing user: ' . $user->name . ' (' . $user->email . ')');
            $user->delete();
        }
        $this->line('✓ Dummy users cleared');
    }
}
