<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UtilityBillPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'amount',
        'paid_on',
        'payment_method',
        'reference_number',
        'notes',
        'recorded_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_on' => 'date'
    ];

    public function bill(): BelongsTo
    {
        return $this->belongsTo(UtilityBill::class, 'bill_id');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getPaymentMethodDisplayAttribute(): string
    {
        return match($this->payment_method) {
            'cash' => 'Cash',
            'check' => 'Check',
            'bank_transfer' => 'Bank Transfer',
            'credit_card' => 'Credit Card',
            'online' => 'Online Payment',
            default => ucfirst($this->payment_method)
        };
    }
}
