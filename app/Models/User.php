<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function tenant()
    {
        return $this->ownerProfile(); // Backward compatibility alias
    }

    public function tenantProfile()
    {
        return $this->ownerProfile(); // Backward compatibility alias
    }

    public function ownerProfile()
    {
        return $this->hasOne(Owner::class);
    }

    public function owner()
    {
        return $this->hasOne(Owner::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'owner_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'owner_id');
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'owner_id');
    }

    public function assignedMaintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'assigned_to');
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'owner_id');
    }

    public function assignedComplaints()
    {
        return $this->hasMany(Complaint::class, 'assigned_to');
    }

    public function notices()
    {
        return $this->hasMany(Notice::class, 'created_by');
    }

    // Helper methods
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isManager()
    {
        return $this->hasRole('manager');
    }

    public function isOwner()
    {
        return $this->hasRole('owner');
    }

    public function isActive()
    {
        return $this->status === 'active';
    }
}
