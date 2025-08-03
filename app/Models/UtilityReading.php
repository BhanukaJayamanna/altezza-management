<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UtilityReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'meter_id',
        'current_reading',
        'previous_reading',
        'consumption',
        'amount',
        'reading_date',
        'billing_period_start',
        'billing_period_end',
        'recorded_by',
        'notes'
    ];

    protected $casts = [
        'current_reading' => 'decimal:2',
        'previous_reading' => 'decimal:2',
        'consumption' => 'decimal:2',
        'amount' => 'decimal:2',
        'reading_date' => 'date',
        'billing_period_start' => 'date',
        'billing_period_end' => 'date'
    ];

    public function meter(): BelongsTo
    {
        return $this->belongsTo(UtilityMeter::class, 'meter_id');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function bill(): HasOne
    {
        return $this->hasOne(UtilityBill::class, 'reading_id');
    }

    // Calculate consumption automatically
    public function calculateConsumption(): void
    {
        $this->consumption = $this->current_reading - $this->previous_reading;
    }

    // Calculate amount based on meter rate
    public function calculateAmount(): void
    {
        if ($this->meter && $this->consumption) {
            $this->amount = $this->consumption * $this->meter->rate_per_unit;
        }
    }

    public function getMonthYearAttribute(): string
    {
        return $this->reading_date->format('m/Y');
    }
}
