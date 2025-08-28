<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'type',
        'apartment_id',
        'owner_id',
        'utility_bill_id',
        'rooftop_reservation_id',
        'billing_period_start',
        'billing_period_end',
        'amount',
        'late_fee',
        'discount',
        'total_amount',
        'due_date',
        'status',
        'description',
        'line_items',
        'created_by',
        'paid_on',
        'payment_method',
        'receipt_file',
    ];

    protected $casts = [
        'billing_period_start' => 'date',
        'billing_period_end' => 'date',
        'due_date' => 'date',
        'paid_on' => 'date',
        'amount' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'line_items' => 'array',
    ];

    // Relationships
    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // Backward compatibility alias
    public function tenant()
    {
        return $this->owner();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function utilityBill()
    {
        return $this->belongsTo(UtilityBill::class);
    }

    public function rooftopReservation()
    {
        return $this->belongsTo(RooftopReservation::class);
    }

    // Helper methods
    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isOverdue()
    {
        return $this->status === 'overdue' || ($this->status === 'pending' && $this->due_date->isPast());
    }

    public function getFormattedAmountAttribute()
    {
        return '$' . number_format($this->total_amount, 2);
    }

    public function getDaysOverdueAttribute()
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        
        return $this->due_date->diffInDays(now());
    }
}
