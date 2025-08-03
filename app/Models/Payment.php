<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'tenant_id',
        'amount',
        'payment_date',
        'method',
        'reference_number',
        'status',
        'notes',
        'receipt_file',
        'recorded_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    /**
     * Get the invoice this payment belongs to
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the tenant who made this payment
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    /**
     * Get the user who recorded this payment
     */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Scope for completed payments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Get payment method label
     */
    public function getMethodLabelAttribute(): string
    {
        return match($this->method) {
            'cash' => 'Cash',
            'bank_transfer' => 'Bank Transfer',
            'cheque' => 'Cheque',
            'online' => 'Online Payment',
            'card' => 'Card Payment',
            default => 'Unknown'
        };
    }

    /**
     * Get status label with color
     */
    public function getStatusLabelAttribute(): array
    {
        return match($this->status) {
            'pending' => ['label' => 'Pending', 'color' => 'yellow'],
            'completed' => ['label' => 'Completed', 'color' => 'green'],
            'failed' => ['label' => 'Failed', 'color' => 'red'],
            'cancelled' => ['label' => 'Cancelled', 'color' => 'gray'],
            'rejected' => ['label' => 'Failed', 'color' => 'red'], // Legacy support
            default => ['label' => 'Unknown', 'color' => 'gray']
        };
    }
}
