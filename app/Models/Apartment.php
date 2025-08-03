<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Apartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'block',
        'floor',
        'type',
        'status',
        'area',
        'rent_amount',
        'description',
        'owner_id',
        'tenant_id',
    ];

    protected $casts = [
        'area' => 'decimal:2',
        'rent_amount' => 'decimal:2',
    ];

    // Relationships
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function tenantProfile()
    {
        return $this->hasOneThrough(Tenant::class, User::class, 'id', 'user_id', 'tenant_id', 'id');
    }

    public function leases()
    {
        return $this->hasMany(Lease::class);
    }

    public function currentLease()
    {
        return $this->hasOne(Lease::class)->where('status', 'active')->orderBy('end_date', 'desc');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function utilityMeters()
    {
        return $this->hasMany(UtilityMeter::class);
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    // Helper methods
    public function isVacant()
    {
        return $this->status === 'vacant';
    }

    public function isOccupied()
    {
        return $this->status === 'occupied';
    }

    public function isUnderMaintenance()
    {
        return $this->status === 'maintenance';
    }

    public function getFullAddressAttribute()
    {
        $address = "Apt {$this->number}";
        if ($this->block) {
            $address .= ", Block {$this->block}";
        }
        if ($this->floor) {
            $address .= ", Floor {$this->floor}";
        }
        return $address;
    }
}
