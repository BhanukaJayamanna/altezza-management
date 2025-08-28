<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class UtilityBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'apartment_id',
        'meter_id',
        'reading_id',
        'invoice_id',
        'type',
        'period',
        'month',
        'year',
        'units_used',
        'price_per_unit',
        'total_amount',
        'status',
        'due_date',
        'paid_date',
        'paid_amount',
        'notes'
    ];

    protected $casts = [
        'units_used' => 'decimal:2',
        'price_per_unit' => 'decimal:4',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date'
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // Backward compatibility alias
    public function tenant(): BelongsTo
    {
        return $this->owner();
    }

    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }

    public function meter(): BelongsTo
    {
        return $this->belongsTo(UtilityMeter::class, 'meter_id');
    }

    public function reading(): BelongsTo
    {
        return $this->belongsTo(UtilityReading::class, 'reading_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(UtilityBillPayment::class, 'bill_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function getRemainingAmountAttribute(): float
    {
        return $this->total_amount - $this->paid_amount;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status !== 'paid' && $this->due_date < now();
    }

    public function getStatusColorAttribute(): string
    {
        if ($this->is_overdue) {
            return 'red';
        }
        
        return match($this->status) {
            'paid' => 'green',
            'partial' => 'yellow',
            'unpaid' => 'blue',
            'overdue' => 'red',
            default => 'gray'
        };
    }

    public function getTypeDisplayAttribute(): string
    {
        return ucfirst($this->type);
    }

    public function getBillingPeriodAttribute(): string
    {
        // Convert from MM/YYYY format to YYYY-MM format for HTML month input
        if (!$this->period) {
            return '';
        }
        
        $parts = explode('/', $this->period);
        if (count($parts) === 2) {
            $month = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
            $year = $parts[1];
            return $year . '-' . $month;
        }
        
        return '';
    }

    public function getUsageAmountAttribute(): float
    {
        return $this->units_used;
    }

    public function getRatePerUnitAttribute(): float
    {
        return $this->price_per_unit;
    }

    public function getBillNumberAttribute(): string
    {
        return 'UB-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    public function isOverdue(): bool
    {
        return $this->is_overdue;
    }

    public function daysOverdue(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        
        return $this->due_date->diffInDays(now());
    }

    public function markAsPaid(float $amount = null, ?string $paymentMethod = 'cash', ?string $reference = null): void
    {
        $amount = $amount ?? $this->remaining_amount;
        
        $this->paid_amount += $amount;
        $this->paid_date = now();
        
        if ($this->paid_amount >= $this->total_amount) {
            $this->status = 'paid';
        } else {
            $this->status = 'partial';
        }
        
        $this->save();

        // Create payment record
        $this->payments()->create([
            'amount' => $amount,
            'paid_on' => now(),
            'payment_method' => $paymentMethod,
            'reference_number' => $reference,
                        'recorded_by' => Auth::id() ?? 1
        ]);
    }
}
