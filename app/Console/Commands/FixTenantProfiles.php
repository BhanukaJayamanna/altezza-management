<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Tenant;

class FixTenantProfiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:fix-profiles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create missing tenant profiles for users with tenant role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for tenant users without tenant profiles...');
        
        $tenantUsers = User::whereHas('roles', function($q) {
            $q->where('name', 'tenant');
        })->whereDoesntHave('tenantProfile')->get();
        
        if ($tenantUsers->count() === 0) {
            $this->info('All tenant users already have tenant profiles.');
            return;
        }
        
        $this->info("Found {$tenantUsers->count()} tenant users without profiles.");
        
        $created = 0;
        foreach ($tenantUsers as $user) {
            $tenantProfile = Tenant::create([
                'user_id' => $user->id,
                'status' => 'active',
            ]);
            
            $this->line("Created tenant profile for: {$user->name} ({$user->email})");
            $created++;
        }
        
        $this->info("Successfully created {$created} tenant profiles.");
    }
}
