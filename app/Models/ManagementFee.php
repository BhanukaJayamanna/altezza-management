<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class ManagementFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'apartment_id',
        'area_sqft',
        'management_fee_ratio',
        'sinking_fund_ratio',
        'monthly_management_fee',
        'monthly_sinking_fund',
        'quarterly_management_fee',
        'quarterly_sinking_fund',
        'total_quarterly_rental',
        'status',
        'effective_from',
        'effective_until',
        'notes',
    ];

    protected $casts = [
        'area_sqft' => 'decimal:2',
        'management_fee_ratio' => 'decimal:2',
        'sinking_fund_ratio' => 'decimal:2',
        'monthly_management_fee' => 'decimal:2',
        'monthly_sinking_fund' => 'decimal:2',
        'quarterly_management_fee' => 'decimal:2',
        'quarterly_sinking_fund' => 'decimal:2',
        'total_quarterly_rental' => 'decimal:2',
        'effective_from' => 'date',
        'effective_until' => 'date',
    ];

    // Relationships
    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function managementFeeInvoices()
    {
        return $this->hasMany(ManagementFeeInvoice::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('effective_from', '<=', now())
                    ->where(function($q) {
                        $q->whereNull('effective_until')
                          ->orWhere('effective_until', '>=', now());
                    });
    }

    public function scopeForApartment($query, $apartmentId)
    {
        return $query->where('apartment_id', $apartmentId);
    }

    // Helper methods
    public function isActive()
    {
        return $this->status === 'active' && 
               $this->effective_from <= now() && 
               ($this->effective_until === null || $this->effective_until >= now());
    }

    public function calculateFees()
    {
        // Monthly calculations
        $this->monthly_management_fee = $this->area_sqft * $this->management_fee_ratio;
        $this->monthly_sinking_fund = $this->area_sqft * $this->sinking_fund_ratio;
        
        // Quarterly calculations (Monthly x 3)
        $this->quarterly_management_fee = $this->monthly_management_fee * 3;
        $this->quarterly_sinking_fund = $this->monthly_sinking_fund * 3;
        
        // Total quarterly rental
        $this->total_quarterly_rental = $this->quarterly_management_fee + $this->quarterly_sinking_fund;
        
        return $this;
    }

    public function recalculateWithNewRatios($managementRatio = null, $sinkingRatio = null)
    {
        if ($managementRatio !== null) {
            $this->management_fee_ratio = $managementRatio;
        }
        
        if ($sinkingRatio !== null) {
            $this->sinking_fund_ratio = $sinkingRatio;
        }
        
        return $this->calculateFees();
    }

    public function getFormattedBreakdownAttribute()
    {
        return [
            'area_sqft' => number_format($this->area_sqft, 2),
            'management_fee_ratio' => number_format($this->management_fee_ratio, 2),
            'sinking_fund_ratio' => number_format($this->sinking_fund_ratio, 2),
            'monthly_management_fee' => number_format($this->monthly_management_fee, 2),
            'monthly_sinking_fund' => number_format($this->monthly_sinking_fund, 2),
            'quarterly_management_fee' => number_format($this->quarterly_management_fee, 2),
            'quarterly_sinking_fund' => number_format($this->quarterly_sinking_fund, 2),
            'total_quarterly_rental' => number_format($this->total_quarterly_rental, 2),
        ];
    }

    public function getCalculationDetailsAttribute()
    {
        return [
            'management_fee_calculation' => "{$this->area_sqft} sq ft × {$this->management_fee_ratio} = {$this->monthly_management_fee}/month",
            'quarterly_management_fee_calculation' => "{$this->monthly_management_fee} × 3 months = {$this->quarterly_management_fee}",
            'sinking_fund_calculation' => "{$this->area_sqft} sq ft × {$this->sinking_fund_ratio} = {$this->monthly_sinking_fund}/month",
            'quarterly_sinking_fund_calculation' => "{$this->monthly_sinking_fund} × 3 months = {$this->quarterly_sinking_fund}",
            'total_calculation' => "{$this->quarterly_management_fee} + {$this->quarterly_sinking_fund} = {$this->total_quarterly_rental}",
        ];
    }

    // Static methods
    public static function createForApartment(Apartment $apartment, $managementRatio = null, $sinkingRatio = null)
    {
        $managementRatio = $managementRatio ?? Setting::getValue('management_fee_ratio', 14.00);
        $sinkingRatio = $sinkingRatio ?? Setting::getValue('sinking_fund_ratio', 2.50);

        // Ensure apartment area is available
        if (!$apartment->area || $apartment->area <= 0) {
            throw new \Exception("Apartment {$apartment->number} must have a valid area (sq ft) to calculate management fees.");
        }

        $managementFee = new static([
            'apartment_id' => $apartment->id,
            'area_sqft' => $apartment->area, // Use apartment's area directly
            'management_fee_ratio' => $managementRatio,
            'sinking_fund_ratio' => $sinkingRatio,
            'status' => 'active',
            'effective_from' => now(),
        ]);

        $managementFee->calculateFees();
        $managementFee->save();

        return $managementFee;
    }

    public static function updateGlobalRatios($managementRatio, $sinkingRatio)
    {
        // Update global settings
        Setting::setValue('management_fee_ratio', $managementRatio, 'decimal', 'management_fees');
        Setting::setValue('sinking_fund_ratio', $sinkingRatio, 'decimal', 'management_fees');

        // Update all active management fees
        $updatedCount = 0;
        static::active()->chunk(100, function($managementFees) use ($managementRatio, $sinkingRatio, &$updatedCount) {
            foreach ($managementFees as $managementFee) {
                $managementFee->recalculateWithNewRatios($managementRatio, $sinkingRatio);
                $managementFee->save();
                $updatedCount++;
            }
        });

        return $updatedCount;
    }

    // Boot method for automatic calculations
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($managementFee) {
            if (!$managementFee->monthly_management_fee) {
                $managementFee->calculateFees();
            }
        });

        static::updating(function ($managementFee) {
            if ($managementFee->isDirty(['area_sqft', 'management_fee_ratio', 'sinking_fund_ratio'])) {
                $managementFee->calculateFees();
            }
        });
    }
}
