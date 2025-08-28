<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Invoice Generation ===\n\n";

// Check current state
echo "1. Current State:\n";
echo "   - Current Quarter: " . \App\Models\ManagementFeeInvoice::getCurrentQuarter() . "\n";
echo "   - Current Year: " . now()->year . "\n";
echo "   - Total Management Fees: " . \App\Models\ManagementFee::count() . "\n";
echo "   - Active Management Fees: " . \App\Models\ManagementFee::where('status', 'active')->count() . "\n";
echo "   - Total Invoices: " . \App\Models\ManagementFeeInvoice::count() . "\n";

// Check active management fees
echo "\n2. Active Management Fees:\n";
$activeFees = \App\Models\ManagementFee::active()->with(['apartment'])->get();
foreach ($activeFees as $fee) {
    echo "   - Apartment {$fee->apartment->number}: Area {$fee->area_sqft} sqft, Total: Rs. {$fee->total_quarterly_rental}\n";
}

// Test invoice generation
echo "\n3. Testing Invoice Generation for Q3 2025:\n";
try {
    $invoices = \App\Models\ManagementFeeInvoice::createForQuarter(3, 2025);
    echo "   SUCCESS: Generated " . $invoices->count() . " invoices!\n";
    
    foreach ($invoices as $invoice) {
        echo "   - Invoice: {$invoice->invoice_number} for Apartment {$invoice->apartment->number} - Rs. {$invoice->total_amount}\n";
    }
    
} catch (\Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n";
    echo "   Stack trace: " . $e->getTraceAsString() . "\n";
}

// Check final state
echo "\n4. Final State:\n";
echo "   - Total Invoices: " . \App\Models\ManagementFeeInvoice::count() . "\n";

echo "\n=== Test Complete ===\n";
