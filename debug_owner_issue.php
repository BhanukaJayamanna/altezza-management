<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Debugging Owner Issue ===\n\n";

// Check users
echo "1. Users in system:\n";
$users = \App\Models\User::all();
foreach ($users as $user) {
    echo "   - ID: {$user->id}, Name: {$user->name}, Email: {$user->email}\n";
}

// Check apartments and their owners
echo "\n2. Apartments and their owners:\n";
$apartments = \App\Models\Apartment::with(['currentOwner'])->get();
foreach ($apartments as $apartment) {
    $owner = $apartment->currentOwner;
    echo "   - Apartment {$apartment->number}: Owner ID: {$apartment->owner_id}, Owner: " . ($owner ? $owner->name : 'NONE') . "\n";
}

// Check management fees
echo "\n3. Management fees:\n";
$managementFees = \App\Models\ManagementFee::with('apartment')->get();
foreach ($managementFees as $fee) {
    echo "   - Apartment {$fee->apartment->number}: Area {$fee->area_sqft} sqft\n";
    echo "     Current owner in apartment: " . ($fee->apartment->currentOwner ? $fee->apartment->currentOwner->name : 'NONE') . "\n";
    echo "     Owner ID in apartment: {$fee->apartment->owner_id}\n";
}

echo "\n=== End Debug ===\n";
