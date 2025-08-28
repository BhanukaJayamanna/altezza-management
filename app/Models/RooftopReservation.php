<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class RooftopReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_number',
        'owner_id',
        'apartment_id',
        'invoice_id',
        'reservation_date',
        'start_time',
        'end_time',
        'duration_hours',
        'event_type',
        'event_title',
        'event_description',
        'number_of_guests',
        'base_rate',
        'base_amount',
        'hourly_rate',
        'guest_charges',
        'cleaning_charge',
        'security_deposit',
        'additional_charges',
        'discount',
        'total_amount',
        'status',
        'deposit_due_date',
        'final_payment_due_date',
        'special_requirements',
        'equipment_requested',
        'catering_allowed',
        'terms_conditions',
        'cancellation_policy',
        'deposit_paid',
        'final_payment_paid',
        'deposit_paid_at',
        'final_payment_paid_at',
        'notes',
        'admin_notes',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'deposit_due_date' => 'date',
        'final_payment_due_date' => 'date',
        'base_rate' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'cleaning_fee' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'additional_charges' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'equipment_requested' => 'array',
        'catering_allowed' => 'array',
        'deposit_paid' => 'boolean',
        'final_payment_paid' => 'boolean',
        'deposit_paid_at' => 'datetime',
        'final_payment_paid_at' => 'datetime',
        'approved_at' => 'datetime',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    // Relationships
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'rooftop_reservation_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'rooftop_reservation_id');
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isConfirmed()
    {
        return $this->status === 'confirmed';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'confirmed']) && 
               $this->reservation_date->isFuture();
    }

    public function getFormattedTimeSlotAttribute()
    {
        return Carbon::parse($this->start_time)->format('h:i A') . ' - ' . 
               Carbon::parse($this->end_time)->format('h:i A');
    }

    public function getEventTypeDisplayAttribute()
    {
        return match($this->event_type) {
            'party' => 'Party',
            'wedding' => 'Wedding',
            'corporate' => 'Corporate Event',
            'family_gathering' => 'Family Gathering',
            'birthday' => 'Birthday Party',
            'other' => 'Other Event',
            default => ucfirst(str_replace('_', ' ', $this->event_type))
        };
    }

    public function getStatusDisplayAttribute()
    {
        return match($this->status) {
            'pending' => 'Pending Approval',
            'approved' => 'Approved',
            'cancelled' => 'Cancelled',
            'completed' => 'Completed',
            default => ucfirst($this->status)
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'completed' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function calculateTotalAmount()
    {
        return $this->base_amount + $this->guest_charges + $this->cleaning_charge + $this->security_deposit + ($this->additional_charges ?? 0) - ($this->discount ?? 0);
    }

    public function getDurationInHours()
    {
        return $this->duration_hours ?? 0;
    }



    public function isDepositDue()
    {
        return !$this->deposit_paid && 
               $this->deposit_due_date && 
               $this->deposit_due_date->isFuture();
    }

    public function isFinalPaymentDue()
    {
        return !$this->final_payment_paid && 
               $this->final_payment_due_date && 
               $this->final_payment_due_date->isFuture();
    }

    public function isOverdue()
    {
        return (!$this->deposit_paid && 
                $this->deposit_due_date && 
                $this->deposit_due_date->isPast()) ||
               (!$this->final_payment_paid && 
                $this->final_payment_due_date && 
                $this->final_payment_due_date->isPast());
    }

    public function getRemainingAmountAttribute()
    {
        $totalPaid = $this->payments()->where('status', 'confirmed')->sum('amount');
        return max(0, $this->total_amount - $totalPaid);
    }

    public function getTotalPaidAttribute()
    {
        return $this->payments()->where('status', 'confirmed')->sum('amount');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'approved'); // For backward compatibility
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('reservation_date', $date);
    }

    public function scopeOverlapping($query, $date, $startTime, $endTime, $excludeId = null)
    {
        $query = $query->where('reservation_date', $date)
                      ->where('status', '!=', 'cancelled')
                      ->where(function($q) use ($startTime, $endTime) {
                          $q->whereBetween('start_time', [$startTime, $endTime])
                            ->orWhereBetween('end_time', [$startTime, $endTime])
                            ->orWhere(function($subQ) use ($startTime, $endTime) {
                                $subQ->where('start_time', '<=', $startTime)
                                     ->where('end_time', '>=', $endTime);
                            });
                      });
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query;
    }

    // Static methods
    public static function generateReservationNumber()
    {
        $year = date('Y');
        $lastReservation = static::whereYear('created_at', $year)
                                ->orderBy('id', 'desc')
                                ->first();
        
        $nextNumber = $lastReservation ? 
                     (intval(substr($lastReservation->reservation_number, -4)) + 1) : 1;
        
        return 'RR-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public static function getAvailableTimeSlots($date, $excludeId = null)
    {
        $occupiedSlots = static::forDate($date)
                              ->where('status', '!=', 'cancelled')
                              ->when($excludeId, function($q) use ($excludeId) {
                                  return $q->where('id', '!=', $excludeId);
                              })
                              ->get(['start_time', 'end_time']);

        // Define available hours (e.g., 8 AM to 11 PM)
        $availableHours = [];
        for ($hour = 8; $hour <= 23; $hour++) {
            $timeSlot = sprintf('%02d:00', $hour);
            $isAvailable = true;
            
            foreach ($occupiedSlots as $slot) {
                $startHour = Carbon::parse($slot->start_time)->hour;
                $endHour = Carbon::parse($slot->end_time)->hour;
                
                if ($hour >= $startHour && $hour < $endHour) {
                    $isAvailable = false;
                    break;
                }
            }
            
            if ($isAvailable) {
                $availableHours[] = $timeSlot;
            }
        }
        
        return $availableHours;
    }
}
