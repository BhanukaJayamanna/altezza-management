<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'apartment_id',
        'lease_start',
        'lease_end',
        'status',
        'id_document',
        'emergency_contact',
        'emergency_phone',
        'notes',
    ];

    protected $casts = [
        'lease_start' => 'date',
        'lease_end' => 'date',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function leases()
    {
        return $this->hasMany(Lease::class, 'tenant_id', 'user_id');
    }

    public function currentLease()
    {
        return $this->hasOne(Lease::class, 'tenant_id', 'user_id')->where('status', 'active');
    }

    // Helper methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function hasMovedOut()
    {
        return $this->status === 'moved_out';
    }

    public function isLeaseExpiring($days = 30)
    {
        if (!$this->lease_end) {
            return false;
        }
        
        return $this->lease_end->diffInDays(Carbon::now()) <= $days && $this->lease_end->isFuture();
    }

    public function isLeaseExpired()
    {
        if (!$this->lease_end) {
            return false;
        }
        
        return $this->lease_end->isPast();
    }

    public function getLeaseDurationAttribute()
    {
        if (!$this->lease_start || !$this->lease_end) {
            return null;
        }
        
        return $this->lease_start->diffInMonths($this->lease_end);
    }
}
