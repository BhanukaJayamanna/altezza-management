<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UtilityBill;
use App\Models\Apartment;
use App\Models\User;
use App\Models\UtilityMeter;
use App\Models\UtilityReading;
use Carbon\Carbon;

class DummyUtilityBillsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get apartments with tenants
        $apartments = Apartment::with('currentLease.tenant')->get();
        
        // Get utility meters
        $meters = UtilityMeter::all();
        
        // Define utility types and their price ranges
        $utilityTypes = [
            'electricity' => ['min_price' => 0.08, 'max_price' => 0.15, 'min_usage' => 200, 'max_usage' => 800],
            'water' => ['min_price' => 0.005, 'max_price' => 0.012, 'min_usage' => 1000, 'max_usage' => 3000],
            'gas' => ['min_price' => 0.06, 'max_price' => 0.12, 'min_usage' => 100, 'max_usage' => 400],
        ];

        // Create bills for the last 6 months
        $startDate = Carbon::now()->subMonths(6);
        
        foreach ($apartments as $apartment) {
            // Skip apartments without tenants
            if (!$apartment->currentLease || !$apartment->currentLease->tenant) {
                continue;
            }

            $tenant = $apartment->currentLease->tenant;
            
            // Create 2-3 bills per apartment for different months
            $billsCount = rand(2, 3);
            
            for ($i = 0; $i < $billsCount; $i++) {
                $billDate = $startDate->copy()->addMonths($i);
                $utilityType = array_rand($utilityTypes);
                $typeConfig = $utilityTypes[$utilityType];
                
                // Find a meter for this apartment and type if available
                $meter = $meters->where('apartment_id', $apartment->id)
                              ->where('type', $utilityType)
                              ->first();
                
                // Skip this bill if no meter is available and meter_id is required
                if (!$meter) {
                    continue;
                }
                
                // Generate random usage and pricing
                $unitsUsed = rand($typeConfig['min_usage'], $typeConfig['max_usage']);
                $pricePerUnit = round(rand($typeConfig['min_price'] * 10000, $typeConfig['max_price'] * 10000) / 10000, 4);
                $totalAmount = round($unitsUsed * $pricePerUnit, 2);
                
                // Random status weighted towards unpaid for newer bills
                $statuses = ['unpaid', 'paid', 'partial', 'overdue'];
                $weights = $billDate->isAfter(Carbon::now()->subMonths(2)) ? [60, 20, 15, 5] : [20, 60, 15, 5];
                $status = $this->weightedRandom($statuses, $weights);
                
                // Create utility reading
                $previousReading = rand(1000, 5000);
                $currentReading = $previousReading + $unitsUsed;
                
                $reading = UtilityReading::create([
                    'meter_id' => $meter->id,
                    'reading_date' => $billDate->copy()->day(rand(1, 5)),
                    'previous_reading' => $previousReading,
                    'current_reading' => $currentReading,
                    'consumption' => $unitsUsed,
                    'amount' => $totalAmount,
                    'billing_period_start' => $billDate->copy()->startOfMonth(),
                    'billing_period_end' => $billDate->copy()->endOfMonth(),
                    'recorded_by' => 1, // Admin user
                    'notes' => 'Automated reading for ' . $billDate->format('F Y'),
                ]);
                
                $readingId = $reading->id;
                
                // Set paid amount based on status
                $paidAmount = 0;
                $paidDate = null;
                if ($status === 'paid') {
                    $paidAmount = $totalAmount;
                    $paidDate = $billDate->copy()->addDays(rand(15, 45));
                } elseif ($status === 'partial') {
                    $paidAmount = round($totalAmount * rand(30, 80) / 100, 2);
                }
                
                UtilityBill::create([
                    'tenant_id' => $tenant->id,
                    'apartment_id' => $apartment->id,
                    'meter_id' => $meter->id,
                    'reading_id' => $readingId,
                    'type' => $utilityType,
                    'period' => $billDate->format('m/Y'),
                    'month' => $billDate->month,
                    'year' => $billDate->year,
                    'units_used' => $unitsUsed,
                    'price_per_unit' => $pricePerUnit,
                    'total_amount' => $totalAmount,
                    'status' => $status,
                    'due_date' => $billDate->copy()->addDays(30),
                    'paid_date' => $paidDate,
                    'paid_amount' => $paidAmount,
                    'notes' => $this->generateRandomNotes($utilityType, $status),
                ]);
            }
        }
        
        $this->command->info('Created dummy utility bills successfully!');
    }
    
    /**
     * Weighted random selection
     */
    private function weightedRandom(array $values, array $weights): mixed
    {
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);
        
        $currentWeight = 0;
        foreach ($values as $index => $value) {
            $currentWeight += $weights[$index];
            if ($random <= $currentWeight) {
                return $value;
            }
        }
        
        return $values[0];
    }
    
    /**
     * Generate random notes for bills
     */
    private function generateRandomNotes(string $type, string $status): ?string
    {
        $notes = [
            'electricity' => [
                'High usage month due to air conditioning',
                'Summer peak usage period',
                'Normal residential consumption',
                'Energy-efficient usage maintained',
                'Peak hour usage detected',
            ],
            'water' => [
                'Standard residential usage',
                'Slightly above average consumption',
                'Water conservation efforts noted',
                'Normal monthly consumption',
                'No leaks detected during inspection',
            ],
            'gas' => [
                'Heating season usage',
                'Hot water and cooking usage',
                'Below average consumption',
                'Efficient gas appliance usage',
                'Standard monthly consumption',
            ],
        ];
        
        $statusNotes = [
            'paid' => ['Payment received on time', 'Auto-payment processed', 'Paid in full'],
            'overdue' => ['Payment reminder sent', 'Second notice required', 'Account past due'],
            'partial' => ['Partial payment received', 'Payment plan in effect', 'Balance remaining'],
            'unpaid' => ['Invoice sent to tenant', 'Awaiting payment', 'Recently generated'],
        ];
        
        // 70% chance of having notes
        if (rand(1, 100) <= 70) {
            $typeNote = $notes[$type][array_rand($notes[$type])];
            $statusNote = $statusNotes[$status][array_rand($statusNotes[$status])];
            
            return rand(1, 2) === 1 ? $typeNote : $statusNote;
        }
        
        return null;
    }
}
