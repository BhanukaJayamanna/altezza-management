<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Owner extends Model
{
    use HasFactory;

    protected $table = 'owners'; // Use the owners table

    protected $fillable = [
        'user_id',
        'apartment_id',
        'status',
        'id_document',
        'emergency_contact',
        'emergency_phone',
        'notes',
    ];

    protected $casts = [
        // Remove lease date casts as they're now in the Lease model
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function apartment()
    {
        return $this->belongsTo(Apartment::class, 'apartment_id');
    }

    // Also add relationship for apartments owned by this owner
    public function apartments()
    {
        return $this->hasMany(Apartment::class, 'owner_id');
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
