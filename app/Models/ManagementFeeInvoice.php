<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ManagementFeeInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'apartment_id',
        'management_fee_id',
        'owner_id',
        'billing_period_start',
        'billing_period_end',
        'quarter',
        'year',
        'area_sqft',
        'management_fee_ratio',
        'sinking_fund_ratio',
        'quarterly_management_fee',
        'quarterly_sinking_fund',
        'total_amount',
        'late_fee',
        'discount',
        'net_total',
        'status',
        'due_date',
        'paid_on',
        'payment_method',
        'payment_reference',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'billing_period_start' => 'date',
        'billing_period_end' => 'date',
        'due_date' => 'date',
        'paid_on' => 'date',
        'area_sqft' => 'decimal:2',
        'management_fee_ratio' => 'decimal:2',
        'sinking_fund_ratio' => 'decimal:2',
        'quarterly_management_fee' => 'decimal:2',
        'quarterly_sinking_fund' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'discount' => 'decimal:2',
        'net_total' => 'decimal:2',
    ];

    // Relationships
    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function managementFee()
    {
        return $this->belongsTo(ManagementFee::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
                    ->orWhere(function($q) {
                        $q->where('status', 'pending')
                          ->where('due_date', '<', now());
                    });
    }

    public function scopeForQuarter($query, $quarter, $year)
    {
        return $query->where('quarter', $quarter)->where('year', $year);
    }

    public function scopeForApartment($query, $apartmentId)
    {
        return $query->where('apartment_id', $apartmentId);
    }

    // Helper methods
    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isOverdue()
    {
        return $this->status === 'overdue' || 
               ($this->status === 'pending' && $this->due_date < now());
    }

    public function markAsPaid($paymentMethod = null, $paymentReference = null)
    {
        $this->update([
            'status' => 'paid',
            'paid_on' => now(),
            'payment_method' => $paymentMethod,
            'payment_reference' => $paymentReference,
        ]);

        return $this;
    }

    public function calculateLateFee()
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        $lateFeePercentage = Setting::getValue('management_fee_late_fee_percentage', 5.00);
        return ($this->total_amount * $lateFeePercentage) / 100;
    }

    public function applyLateFee()
    {
        if ($this->isOverdue() && $this->late_fee == 0) {
            $lateFee = $this->calculateLateFee();
            $this->update([
                'late_fee' => $lateFee,
                'net_total' => $this->total_amount + $lateFee - $this->discount,
            ]);
        }

        return $this;
    }

    public function applyDiscount($discountAmount)
    {
        $this->update([
            'discount' => $discountAmount,
            'net_total' => $this->total_amount + $this->late_fee - $discountAmount,
        ]);

        return $this;
    }

    public function getQuarterNameAttribute()
    {
        return match($this->quarter) {
            1 => 'Q1 (Jan-Mar)',
            2 => 'Q2 (Apr-Jun)',
            3 => 'Q3 (Jul-Sep)',
            4 => 'Q4 (Oct-Dec)',
            default => "Q{$this->quarter}",
        };
    }

    public function getFormattedPeriodAttribute()
    {
        return $this->billing_period_start->format('M j, Y') . ' - ' . 
               $this->billing_period_end->format('M j, Y');
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'paid' => 'success',
            'overdue' => 'danger',
            'cancelled' => 'secondary',
            default => 'primary',
        };
    }

    public function getDaysOverdueAttribute()
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        return now()->diffInDays($this->due_date);
    }

    public function getBreakdownAttribute()
    {
        return [
            'management_fee' => [
                'calculation' => "{$this->area_sqft} sq ft × {$this->management_fee_ratio} × 3 months",
                'amount' => $this->quarterly_management_fee,
            ],
            'sinking_fund' => [
                'calculation' => "{$this->area_sqft} sq ft × {$this->sinking_fund_ratio} × 3 months",
                'amount' => $this->quarterly_sinking_fund,
            ],
            'subtotal' => $this->total_amount,
            'late_fee' => $this->late_fee,
            'discount' => $this->discount,
            'net_total' => $this->net_total,
        ];
    }

    // Static methods
    public static function generateInvoiceNumber($quarter, $year, $apartmentId)
    {
        $prefix = "MF";
        $apartmentNumber = Apartment::find($apartmentId)->number ?? $apartmentId;
        return "{$prefix}{$year}Q{$quarter}-{$apartmentNumber}-" . time();
    }

    public static function createForQuarter($quarter, $year, $apartmentId = null, $createdBy = null)
    {
        $query = ManagementFee::active();
        
        if ($apartmentId) {
            $query->where('apartment_id', $apartmentId);
        }

        $managementFees = $query->with(['apartment', 'apartment.currentOwner'])->get();
        $createdInvoices = [];

        foreach ($managementFees as $managementFee) {
            // Check if invoice already exists for this quarter
            $existingInvoice = static::forQuarter($quarter, $year)
                                   ->forApartment($managementFee->apartment_id)
                                   ->first();

            if ($existingInvoice) {
                continue; // Skip if already exists
            }

            // Get current owner for the apartment
            $apartment = $managementFee->apartment;
            $owner = $apartment->currentOwner;

            // Ensure apartment has valid area
            if (!$apartment->area || $apartment->area <= 0) {
                Log::warning("Skipping invoice generation for apartment {$apartment->number} - no valid area");
                continue;
            }

            // Use current apartment area (sync with latest data)
            $currentArea = $apartment->area;
            
            // Recalculate fees based on current apartment area if different
            if ($currentArea != $managementFee->area_sqft) {
                $managementFee->area_sqft = $currentArea;
                $managementFee->calculateFees();
                $managementFee->save();
            }

            // Calculate billing period
            $billingPeriod = static::getQuarterDates($quarter, $year);
            $dueDate = $billingPeriod['end']->addDays(Setting::getValue('management_fee_due_days', 30));

            $invoice = static::create([
                'invoice_number' => static::generateInvoiceNumber($quarter, $year, $managementFee->apartment_id),
                'apartment_id' => $managementFee->apartment_id,
                'management_fee_id' => $managementFee->id,
                'owner_id' => $owner?->id,
                'billing_period_start' => $billingPeriod['start'],
                'billing_period_end' => $billingPeriod['end'],
                'quarter' => $quarter,
                'year' => $year,
                'area_sqft' => $currentArea, // Use current apartment area
                'management_fee_ratio' => $managementFee->management_fee_ratio,
                'sinking_fund_ratio' => $managementFee->sinking_fund_ratio,
                'quarterly_management_fee' => $managementFee->quarterly_management_fee,
                'quarterly_sinking_fund' => $managementFee->quarterly_sinking_fund,
                'total_amount' => $managementFee->total_quarterly_rental,
                'net_total' => $managementFee->total_quarterly_rental,
                'due_date' => $dueDate,
                'created_by' => $createdBy ?? (Auth::check() ? Auth::id() : 1),
            ]);

            $createdInvoices[] = $invoice;
        }

        return collect($createdInvoices);
    }

    public static function getQuarterDates($quarter, $year)
    {
        $quarters = [
            1 => ['start' => Carbon::create($year, 1, 1), 'end' => Carbon::create($year, 3, 31)],
            2 => ['start' => Carbon::create($year, 4, 1), 'end' => Carbon::create($year, 6, 30)],
            3 => ['start' => Carbon::create($year, 7, 1), 'end' => Carbon::create($year, 9, 30)],
            4 => ['start' => Carbon::create($year, 10, 1), 'end' => Carbon::create($year, 12, 31)],
        ];

        return $quarters[$quarter] ?? null;
    }

    public static function getCurrentQuarter()
    {
        $month = now()->month;
        return match(true) {
            $month >= 1 && $month <= 3 => 1,
            $month >= 4 && $month <= 6 => 2,
            $month >= 7 && $month <= 9 => 3,
            $month >= 10 && $month <= 12 => 4,
        };
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (!$invoice->net_total) {
                $invoice->net_total = $invoice->total_amount + $invoice->late_fee - $invoice->discount;
            }
        });

        static::updating(function ($invoice) {
            if ($invoice->isDirty(['total_amount', 'late_fee', 'discount'])) {
                $invoice->net_total = $invoice->total_amount + $invoice->late_fee - $invoice->discount;
            }
        });
    }
}
