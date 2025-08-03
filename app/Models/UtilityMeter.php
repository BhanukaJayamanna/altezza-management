<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UtilityMeter extends Model
{
    use HasFactory;

    protected $fillable = [
        'apartment_id',
        'type',
        'meter_number',
        'last_reading',
        'last_reading_date',
        'rate_per_unit',
        'status',
        'notes'
    ];

    protected $casts = [
        'last_reading' => 'decimal:2',
        'rate_per_unit' => 'decimal:4',
        'last_reading_date' => 'date'
    ];

    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }

    public function readings(): HasMany
    {
        return $this->hasMany(UtilityReading::class, 'meter_id');
    }

    public function bills(): HasMany
    {
        return $this->hasMany(UtilityBill::class, 'meter_id');
    }

    public function getLatestReading()
    {
        return $this->readings()->orderBy('reading_date', 'desc')->first();
    }

    public function getTypeDisplayAttribute(): string
    {
        return ucfirst($this->type);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'green',
            'inactive' => 'red',
            'faulty' => 'yellow',
            default => 'gray'
        };
    }
}
