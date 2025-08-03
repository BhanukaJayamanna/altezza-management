<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Lease extends Model
{
    use HasFactory;

    protected $fillable = [
        'apartment_id',
        'tenant_id',
        'owner_id',
        'lease_number',
        'start_date',
        'end_date',
        'rent_amount',
        'security_deposit',
        'maintenance_charge',
        'terms_conditions',
        'contract_file',
        'status',
        'renewal_notice_sent',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'renewal_notice_sent' => 'date',
        'rent_amount' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'maintenance_charge' => 'decimal:2',
        'terms_conditions' => 'array',
    ];

    // Relationships
    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    // Helper methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isExpired()
    {
        return $this->end_date && $this->end_date->isPast();
    }

    public function isExpiring($days = 30)
    {
        if (!$this->end_date) {
            return false;
        }
        
        return $this->end_date->diffInDays(Carbon::now()) <= $days && $this->end_date->isFuture();
    }

    public function getDurationInMonthsAttribute()
    {
        if (!$this->start_date || !$this->end_date) {
            return null;
        }
        
        return $this->start_date->diffInMonths($this->end_date);
    }

    public function getRemainingDaysAttribute()
    {
        if (!$this->end_date) {
            return null;
        }
        
        return max(0, Carbon::now()->diffInDays($this->end_date, false));
    }

    public function getTotalValueAttribute()
    {
        if (!$this->rent_amount || !$this->duration_in_months) {
            return 0;
        }
        
        return $this->rent_amount * $this->duration_in_months;
    }

    // Scope methods
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpiring($query, $days = 30)
    {
        return $query->where('end_date', '<=', Carbon::now()->addDays($days))
                     ->where('end_date', '>=', Carbon::now())
                     ->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', Carbon::now())
                     ->where('status', 'active');
    }
}
