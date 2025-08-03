<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Owner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'id_document',
        'bank_details',
        'status',
    ];

    protected $casts = [
        'bank_details' => 'array',
    ];

    // Relationships
    public function apartments()
    {
        return $this->hasMany(Apartment::class);
    }

    public function leases()
    {
        return $this->hasMany(Lease::class);
    }

    // Helper methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function getTotalApartmentsAttribute()
    {
        return $this->apartments()->count();
    }

    public function getOccupiedApartmentsAttribute()
    {
        return $this->apartments()->where('status', 'occupied')->count();
    }

    public function getVacantApartmentsAttribute()
    {
        return $this->apartments()->where('status', 'vacant')->count();
    }
}
