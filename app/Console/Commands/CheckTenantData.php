<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckTenantData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:check-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check tenant data for debugging';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Tenant Data Report:');
        $this->line('-------------------');
        
        $tenantUsers = User::whereHas('roles', function($q) {
            $q->where('name', 'tenant');
        })->with(['tenantProfile', 'tenantProfile.apartment'])->get();
        
        foreach ($tenantUsers as $user) {
            $this->line("User: {$user->name}");
            $this->line("  Email: {$user->email}");
            $this->line("  Phone: " . ($user->phone ?? 'NULL'));
            $this->line("  Has Tenant Profile: " . ($user->tenantProfile ? 'YES' : 'NO'));
            
            if ($user->tenantProfile) {
                $this->line("  ID Document: " . ($user->tenantProfile->id_document ?? 'NULL'));
                $this->line("  Apartment: " . ($user->tenantProfile->apartment ? $user->tenantProfile->apartment->number : 'NULL'));
                $this->line("  Lease Start: " . ($user->tenantProfile->lease_start ?? 'NULL'));
                $this->line("  Lease End: " . ($user->tenantProfile->lease_end ?? 'NULL'));
                $this->line("  Status: " . ($user->tenantProfile->status ?? 'NULL'));
            }
            
            $this->line("  Total Leases: " . $user->leases()->count());
            $this->line('---');
        }
        
        $this->info("Total tenant users: {$tenantUsers->count()}");
    }
}
