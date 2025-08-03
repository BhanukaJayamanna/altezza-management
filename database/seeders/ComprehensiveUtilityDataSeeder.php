<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UtilityBill;
use App\Models\UtilityMeter;
use App\Models\UtilityReading;
use App\Models\Apartment;
use App\Models\User;
use Carbon\Carbon;

class ComprehensiveUtilityDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, create more utility meters for different apartments
        $this->createUtilityMeters();
        
        // Then create utility bills
        $this->createUtilityBills();
        
        $this->command->info('Comprehensive utility data created successfully!');
    }
    
    private function createUtilityMeters(): void
    {
        $apartments = Apartment::all();
        $utilityTypes = ['electricity', 'water', 'gas'];
        
        // Create meters for first 8 apartments (mix of types)
        foreach ($apartments->take(8) as $index => $apartment) {
            // Each apartment gets 1-2 random utility types
            $typesToCreate = collect($utilityTypes)->random(rand(1, 2));
            
            foreach ($typesToCreate as $type) {
                // Check if meter already exists for this apartment and type
                $existingMeter = UtilityMeter::where('apartment_id', $apartment->id)
                                           ->where('type', $type)
                                           ->first();
                
                if (!$existingMeter) {
                    UtilityMeter::create([
                        'apartment_id' => $apartment->id,
                        'meter_number' => strtoupper($type[0]) . str_pad($apartment->id, 3, '0', STR_PAD_LEFT) . rand(100, 999),
                        'type' => $type,
                        'last_reading' => rand(1000, 5000),
                        'last_reading_date' => Carbon::now()->subDays(rand(1, 30)),
                        'rate_per_unit' => $this->getDefaultRate($type),
                        'status' => 'active',
                        'notes' => 'Auto-generated meter for apartment ' . $apartment->number,
                    ]);
                }
            }
        }
        
        $this->command->info('Created utility meters for apartments');
    }
    
    private function createUtilityBills(): void
    {
        // Get apartments with tenants and meters
        $apartments = Apartment::with(['currentLease.tenant', 'utilityMeters'])->get();
        
        // Define utility types and their price ranges
        $utilityTypes = [
            'electricity' => ['min_price' => 0.08, 'max_price' => 0.15, 'min_usage' => 200, 'max_usage' => 800],
            'water' => ['min_price' => 0.005, 'max_price' => 0.012, 'min_usage' => 1000, 'max_usage' => 3000],
            'gas' => ['min_price' => 0.06, 'max_price' => 0.12, 'min_usage' => 100, 'max_usage' => 400],
        ];

        // Create bills for the last 4 months
        for ($monthsBack = 3; $monthsBack >= 0; $monthsBack--) {
            $billDate = Carbon::now()->subMonths($monthsBack);
            
            foreach ($apartments as $apartment) {
                // Skip apartments without tenants
                if (!$apartment->currentLease || !$apartment->currentLease->tenant) {
                    continue;
                }

                $tenant = $apartment->currentLease->tenant;
                
                // Create bills for each meter in this apartment
                foreach ($apartment->utilityMeters as $meter) {
                    $utilityType = $meter->type;
                    $typeConfig = $utilityTypes[$utilityType];
                    
                    // Generate random usage and pricing
                    $unitsUsed = rand($typeConfig['min_usage'], $typeConfig['max_usage']);
                    $pricePerUnit = round(rand($typeConfig['min_price'] * 10000, $typeConfig['max_price'] * 10000) / 10000, 4);
                    $totalAmount = round($unitsUsed * $pricePerUnit, 2);
                    
                    // Status distribution: more recent bills are more likely to be unpaid
                    $statuses = ['unpaid', 'paid', 'partial', 'overdue'];
                    if ($monthsBack <= 1) {
                        // Recent bills (last 2 months)
                        $weights = [50, 30, 15, 5];
                    } else {
                        // Older bills (more likely to be paid)
                        $weights = [10, 70, 15, 5];
                    }
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
                        'notes' => 'Reading for ' . $billDate->format('F Y'),
                    ]);
                    
                    // Set paid amount and date based on status
                    $paidAmount = 0;
                    $paidDate = null;
                    if ($status === 'paid') {
                        $paidAmount = $totalAmount;
                        $paidDate = $billDate->copy()->addDays(rand(15, 45));
                    } elseif ($status === 'partial') {
                        $paidAmount = round($totalAmount * rand(30, 80) / 100, 2);
                        $paidDate = $billDate->copy()->addDays(rand(10, 30));
                    }
                    
                    // Skip if bill already exists for this period and meter
                    $existingBill = UtilityBill::where('meter_id', $meter->id)
                                              ->where('period', $billDate->format('m/Y'))
                                              ->first();
                    
                    if (!$existingBill) {
                        UtilityBill::create([
                            'tenant_id' => $tenant->id,
                            'apartment_id' => $apartment->id,
                            'meter_id' => $meter->id,
                            'reading_id' => $reading->id,
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
            }
        }
    }
    
    private function getDefaultRate(string $type): float
    {
        $rates = [
            'electricity' => 0.12,
            'water' => 0.008,
            'gas' => 0.09,
        ];
        
        return $rates[$type] ?? 0.10;
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
                'Usage within normal range',
            ],
            'water' => [
                'Standard residential usage',
                'Slightly above average consumption',
                'Water conservation efforts noted',
                'Normal monthly consumption',
                'No leaks detected during inspection',
                'Efficient water usage',
            ],
            'gas' => [
                'Heating season usage',
                'Hot water and cooking usage',
                'Below average consumption',
                'Efficient gas appliance usage',
                'Standard monthly consumption',
                'Winter heating usage',
            ],
        ];
        
        $statusNotes = [
            'paid' => ['Payment received on time', 'Auto-payment processed', 'Paid in full', 'Thank you for prompt payment'],
            'overdue' => ['Payment reminder sent', 'Second notice required', 'Account past due', 'Late fee may apply'],
            'partial' => ['Partial payment received', 'Payment plan in effect', 'Balance remaining', 'Installment payment'],
            'unpaid' => ['Invoice sent to tenant', 'Awaiting payment', 'Recently generated', 'Payment due soon'],
        ];
        
        // 80% chance of having notes
        if (rand(1, 100) <= 80) {
            $typeNote = $notes[$type][array_rand($notes[$type])];
            $statusNote = $statusNotes[$status][array_rand($statusNotes[$status])];
            
            return rand(1, 2) === 1 ? $typeNote : $statusNote;
        }
        
        return null;
    }
}
