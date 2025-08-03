<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class UtilityUnitPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'price_per_unit',
        'effective_from',
        'effective_to',
        'description',
        'is_active'
    ];

    protected $casts = [
        'price_per_unit' => 'decimal:4',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'is_active' => 'boolean'
    ];

    public function getEffectiveUntilAttribute()
    {
        return $this->effective_to;
    }

    public function getStatusAttribute(): string
    {
        return $this->is_active ? 'active' : 'inactive';
    }

    public function isCurrent(): bool
    {
        $now = now();
        return $this->is_active && 
               $this->effective_from <= $now && 
               ($this->effective_to === null || $this->effective_to >= $now);
    }

    public static function getCurrentPrice(string $type, ?Carbon $date = null): ?float
    {
        $date = $date ?? now();
        
        $price = self::where('type', $type)
            ->where('effective_from', '<=', $date)
            ->where(function($query) use ($date) {
                $query->whereNull('effective_to')
                      ->orWhere('effective_to', '>=', $date);
            })
            ->where('is_active', true)
            ->orderBy('effective_from', 'desc')
            ->first();

        return $price ? $price->price_per_unit : null;
    }

    public function getTypeDisplayAttribute(): string
    {
        return ucfirst($this->type);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'green',
            'inactive' => 'gray',
            default => 'gray'
        };
    }
}
