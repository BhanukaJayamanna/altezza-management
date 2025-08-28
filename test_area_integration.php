<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Apartment Area Integration ===\n\n";

// Test 1: Check apartments with area
echo "1. Checking apartments with area:\n";
$apartments = \App\Models\Apartment::whereNotNull('area')->where('area', '>', 0)->get();
foreach ($apartments as $apartment) {
    echo "   - Apartment {$apartment->number}: {$apartment->area} sq ft\n";
}

// Test 2: Check management fees and their area sync
echo "\n2. Checking management fees area sync:\n";
$managementFees = \App\Models\ManagementFee::with('apartment')->get();
foreach ($managementFees as $fee) {
    $apartmentArea = $fee->apartment->area ?? 0;
    $feeArea = $fee->area_sqft ?? 0;
    $synced = ($apartmentArea == $feeArea) ? "✓ SYNCED" : "✗ NOT SYNCED";
    echo "   - Apartment {$fee->apartment->number}: Apartment Area: {$apartmentArea}, Fee Area: {$feeArea} {$synced}\n";
}

// Test 3: Create a new management fee for an apartment
echo "\n3. Testing management fee creation:\n";
$testApartment = \App\Models\Apartment::whereNotNull('area')->where('area', '>', 0)->first();
if ($testApartment) {
    echo "   Testing with apartment {$testApartment->number} (Area: {$testApartment->area} sq ft)\n";
    
    try {
        // Delete existing management fee for this apartment if exists
        \App\Models\ManagementFee::where('apartment_id', $testApartment->id)->delete();
        
        $managementFee = \App\Models\ManagementFee::createForApartment($testApartment);
        echo "   ✓ Management fee created successfully!\n";
        echo "     - Area used: {$managementFee->area_sqft} sq ft\n";
        echo "     - Monthly management fee: Rs. {$managementFee->monthly_management_fee}\n";
        echo "     - Monthly sinking fund: Rs. {$managementFee->monthly_sinking_fund}\n";
        echo "     - Quarterly total: Rs. {$managementFee->total_quarterly_rental}\n";
        
    } catch (\Exception $e) {
        echo "   ✗ Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "   No apartments with valid area found\n";
}

// Test 4: Test area update sync
echo "\n4. Testing area update synchronization:\n";
if ($testApartment) {
    $originalArea = $testApartment->area;
    $newArea = $originalArea + 100; // Add 100 sq ft
    
    echo "   Updating apartment {$testApartment->number} area from {$originalArea} to {$newArea} sq ft\n";
    
    $testApartment->update(['area' => $newArea]);
    
    // Check if management fee was updated
    $managementFee = \App\Models\ManagementFee::where('apartment_id', $testApartment->id)->first();
    if ($managementFee) {
        $managementFee->refresh();
        if ($managementFee->area_sqft == $newArea) {
            echo "   ✓ Management fee area synced successfully: {$managementFee->area_sqft} sq ft\n";
            echo "   ✓ New quarterly total: Rs. {$managementFee->total_quarterly_rental}\n";
        } else {
            echo "   ✗ Management fee area NOT synced: {$managementFee->area_sqft} sq ft\n";
        }
    }
    
    // Restore original area
    $testApartment->update(['area' => $originalArea]);
    echo "   Area restored to original: {$originalArea} sq ft\n";
}

echo "\n=== Area Integration Test Complete ===\n";
