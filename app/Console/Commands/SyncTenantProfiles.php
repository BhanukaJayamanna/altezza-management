<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Lease;

class SyncTenantProfiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:sync-profiles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync tenant profiles with active lease data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Syncing tenant profiles with active lease data...');
        
        $activeLeases = Lease::where('status', 'active')->with(['tenant.tenantProfile', 'apartment'])->get();
        
        $synced = 0;
        foreach ($activeLeases as $lease) {
            if ($lease->tenant && $lease->tenant->tenantProfile) {
                $lease->tenant->tenantProfile->update([
                    'apartment_id' => $lease->apartment_id,
                    'lease_start' => $lease->start_date,
                    'lease_end' => $lease->end_date,
                ]);
                
                $apartmentNumber = $lease->apartment ? $lease->apartment->number : 'N/A';
                $this->line("Synced profile for {$lease->tenant->name} - Apt: {$apartmentNumber}");
                $synced++;
            }
        }
        
        $this->info("Successfully synced {$synced} tenant profiles.");
    }
}
