# Apartment Area Integration for Management Fees

## Overview
The management fee system now fully integrates with apartment area data to ensure accurate calculations and proper synchronization between apartments, owners, and invoices.

## Key Features Implemented

### 1. Automatic Area Synchronization
- **Apartment Model Events**: When an apartment's area is updated, all active management fees for that apartment are automatically updated with the new area
- **Real-time Calculations**: Management fees are recalculated immediately when area changes
- **Logging**: All area sync operations are logged for audit purposes

### 2. Enhanced Invoice Generation
- **Current Area Usage**: Invoice generation always uses the apartment's current area, not cached values
- **Owner Synchronization**: Invoices automatically sync with the apartment's current owner
- **Validation**: Apartments without valid area (≤ 0) are skipped with appropriate warnings

### 3. Improved Manual Entry System
- **Area Display**: Manual entry modal shows apartment area when selected
- **Proper Calculations**: Manual entries use actual apartment area for ratio calculations
- **Validation**: Prevents manual entries for apartments without valid area

### 4. Enhanced User Interface
- **Area Column**: Quarterly invoices table now displays apartment area for each invoice
- **Visual Feedback**: Area information is prominently displayed in manual entry form
- **Real-time Updates**: Area display updates immediately when apartment is selected

## Technical Implementation

### Model Changes

#### Apartment Model (`app/Models/Apartment.php`)
```php
// Boot method enhanced with area sync
static::updated(function ($apartment) {
    if ($apartment->wasChanged('area')) {
        $apartment->syncAreaWithManagementFees();
    }
});

// New method for syncing area changes
public function syncAreaWithManagementFees()
{
    if (!$this->area || $this->area <= 0) {
        return;
    }

    $activeManagementFees = $this->managementFees()->where('status', 'active')->get();
    
    foreach ($activeManagementFees as $managementFee) {
        $managementFee->update(['area_sqft' => $this->area]);
        // Model's boot method automatically recalculates fees
    }
}
```

#### ManagementFee Model (`app/Models/ManagementFee.php`)
```php
// Enhanced createForApartment method with validation
public static function createForApartment(Apartment $apartment, $managementRatio = null, $sinkingRatio = null)
{
    // Validation: Ensure apartment has valid area
    if (!$apartment->area || $apartment->area <= 0) {
        throw new \Exception("Apartment {$apartment->number} must have a valid area (sq ft) to calculate management fees.");
    }

    // Use apartment's area directly
    $managementFee = new static([
        'apartment_id' => $apartment->id,
        'area_sqft' => $apartment->area,
        // ... other fields
    ]);
}
```

#### ManagementFeeInvoice Model (`app/Models/ManagementFeeInvoice.php`)
```php
// Enhanced createForQuarter method with area sync
public static function createForQuarter($quarter, $year, $apartmentId = null, $createdBy = null)
{
    foreach ($managementFees as $managementFee) {
        $apartment = $managementFee->apartment;
        
        // Use current apartment area (sync with latest data)
        $currentArea = $apartment->area;
        
        // Recalculate fees if apartment area changed
        if ($currentArea != $managementFee->area_sqft) {
            $managementFee->area_sqft = $currentArea;
            $managementFee->calculateFees();
            $managementFee->save();
        }
        
        // Create invoice with current area
        $invoice = static::create([
            'area_sqft' => $currentArea,
            // ... other fields
        ]);
    }
}
```

### Controller Changes

#### ManagementFeeController (`app/Http/Controllers/ManagementFeeController.php`)
```php
// Enhanced manual entry with area validation
public function manualEntry(Request $request)
{
    $apartment = Apartment::with('currentOwner')->findOrFail($request->apartment_id);
    
    // Ensure apartment has valid area
    if (!$apartment->area || $apartment->area <= 0) {
        throw new \Exception("Apartment {$apartment->number} must have a valid area (sq ft) to calculate management fees.");
    }

    // Use apartment's actual area
    $apartmentArea = $apartment->area;
    
    $invoice = ManagementFeeInvoice::create([
        'area_sqft' => $apartmentArea,
        'management_fee_ratio' => $apartmentArea > 0 ? $request->management_fee / ($apartmentArea * 3) : 0,
        // ... other fields
    ]);
}
```

### View Enhancements

#### Quarterly Invoices View (`resources/views/management-fees/quarterly-invoices.blade.php`)
- **Added Area Column**: Shows apartment area for each invoice
- **Enhanced Manual Entry Modal**: Displays selected apartment's area
- **JavaScript Integration**: Real-time area display when apartment is selected

## Data Flow

```
1. User Updates Apartment Area
   ↓
2. Apartment Model Event Triggered
   ↓
3. syncAreaWithManagementFees() Called
   ↓
4. Active Management Fees Updated
   ↓
5. Fees Automatically Recalculated
   ↓
6. Future Invoices Use New Area
```

## Benefits

### For Users
- **Accurate Billing**: Management fees always reflect current apartment area
- **Transparent Calculations**: Area information is visible throughout the system
- **Easy Management**: Area changes automatically propagate to all related records

### For System Integrity
- **Data Consistency**: Area information is synchronized across all related models
- **Audit Trail**: All area changes are logged for tracking
- **Validation**: Prevents creation of fees/invoices for apartments without valid area

### For Maintenance
- **Automatic Sync**: No manual intervention required when apartment areas change
- **Error Prevention**: Built-in validation prevents invalid calculations
- **Scalable**: System handles area updates efficiently for multiple apartments

## Testing Verification

The integration has been tested and verified:
- ✅ Area synchronization between apartments and management fees
- ✅ Automatic recalculation when area changes
- ✅ Invoice generation with current area data
- ✅ Manual entry validation and calculations
- ✅ User interface updates and area display

## Usage Examples

### Updating Apartment Area
```php
$apartment = Apartment::find(1);
$apartment->update(['area' => 1200]); // Automatically syncs to management fees
```

### Creating Management Fee for Apartment
```php
$apartment = Apartment::find(1);
$managementFee = ManagementFee::createForApartment($apartment);
// Automatically uses $apartment->area for calculations
```

### Generating Quarterly Invoices
```php
$invoices = ManagementFeeInvoice::createForQuarter(1, 2024);
// Each invoice uses current apartment area, not cached values
```

This implementation ensures that apartment area is the single source of truth for all management fee calculations, providing accurate and synchronized billing across the entire system.
