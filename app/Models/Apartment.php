<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;

class Apartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'assessment_no',
        'type',
        'status',
        'area',
        'rent_amount',
        'description',
        'management_corporation_id',
        'owner_id', // Add owner_id to fillable fields
    ];

    protected $casts = [
        'area' => 'decimal:2',
        'rent_amount' => 'decimal:2',
    ];

    // Boot method to handle model events
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($apartment) {
            // Handle status auto-update when owner changes
            if ($apartment->isDirty('owner_id')) {
                $oldOwnerId = $apartment->getOriginal('owner_id');
                $newOwnerId = $apartment->owner_id;

                // If owner is being assigned and status is vacant, change to occupied
                if (!$oldOwnerId && $newOwnerId && $apartment->status === 'vacant') {
                    $apartment->status = 'occupied';
                }
                // If owner is being removed and status is occupied, change to vacant
                elseif ($oldOwnerId && !$newOwnerId && $apartment->status === 'occupied') {
                    $apartment->status = 'vacant';
                }
            }
        });

        static::updated(function ($apartment) {
            // Sync the reverse relationship after update
            if ($apartment->wasChanged('owner_id')) {
                $apartment->syncOwnerRelationship();
            }

            // Sync area changes with management fees
            if ($apartment->wasChanged('area')) {
                $apartment->syncAreaWithManagementFees();
            }
        });

        static::created(function ($apartment) {
            // Sync relationship and status for new apartments
            if ($apartment->owner_id) {
                $apartment->syncOwnerRelationship();
                if ($apartment->status === 'vacant') {
                    $apartment->update(['status' => 'occupied']);
                }
            }
        });
    }

    // Relationships
    public function managementCorporation()
    {
        return $this->belongsTo(ManagementCorporation::class);
    }

    // Owner relationship (since tenants were replaced with owners)
    public function currentOwner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    public function owners()
    {
        return $this->belongsTo(Owner::class, 'owner_id'); // Changed from hasMany to belongsTo for current owner
    }

    // Backward compatibility alias
    public function owner()
    {
        return $this->currentOwner();
    }

    // Alias for backward compatibility (currentTenant is now currentOwner)
    public function currentTenant()
    {
        return $this->currentOwner();
    }

    public function currentTenantProfile()
    {
        return $this->currentOwner();
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

    public function managementFees()
    {
        return $this->hasMany(ManagementFee::class);
    }

    public function currentManagementFee()
    {
        return $this->hasOne(ManagementFee::class)->active()->latest();
    }

    public function managementFeeInvoices()
    {
        return $this->hasMany(ManagementFeeInvoice::class);
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

    public function updateStatus()
    {
        // Status can be manually managed
        // Default behavior: apartments are vacant unless explicitly set
        if ($this->status === null) {
            $this->update(['status' => 'vacant']);
        }
    }

    public function updateStatusBasedOnOwner()
    {
        // Auto-update status based on owner assignment
        if ($this->owner_id && $this->status === 'vacant') {
            $this->update(['status' => 'occupied']);
            return 'occupied';
        } elseif (!$this->owner_id && $this->status === 'occupied') {
            $this->update(['status' => 'vacant']);
            return 'vacant';
        }
        return $this->status;
    }

    public function syncOwnerRelationship()
    {
        // Sync the bidirectional relationship
        if ($this->owner_id) {
            Owner::where('id', $this->owner_id)->update(['apartment_id' => $this->id]);
        }
        
        // Clear old relationships if owner changed
        Owner::where('apartment_id', $this->id)
            ->where('id', '!=', $this->owner_id ?? 0)
            ->update(['apartment_id' => null]);
    }

    /**
     * Sync apartment area changes with active management fees
     */
    public function syncAreaWithManagementFees()
    {
        if (!$this->area || $this->area <= 0) {
            return;
        }

        // Update active management fees with new area
        $activeManagementFees = $this->managementFees()->where('status', 'active')->get();
        
        foreach ($activeManagementFees as $managementFee) {
            $managementFee->update([
                'area_sqft' => $this->area
            ]);
            // The model's boot method will automatically recalculate fees
        }

        Log::info("Synced area ({$this->area} sq ft) for apartment {$this->number} with " . $activeManagementFees->count() . " active management fees");
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
