<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class PaymentVoucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'voucher_number',
        'voucher_date',
        'vendor_name',
        'vendor_phone',
        'vendor_email',
        'vendor_address',
        'description',
        'amount',
        'apartment_id',
        'expense_category',
        'payment_method',
        'reference_number',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'approval_notes',
        'receipt_file',
        'payment_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'voucher_date' => 'date',
        'approved_at' => 'datetime',
        'payment_date' => 'date',
    ];

    /**
     * Auto-generate voucher number on creation
     */
    protected static function booted()
    {
        static::creating(function ($voucher) {
            if (empty($voucher->voucher_number)) {
                $voucher->voucher_number = static::generateVoucherNumber();
            }
        });
    }

    /**
     * Generate unique voucher number
     */
    public static function generateVoucherNumber(): string
    {
        $year = date('Y');
        $prefix = "PV-{$year}-";
        
        // Get the last voucher number for this year
        $lastVoucher = static::where('voucher_number', 'like', $prefix . '%')
            ->orderBy('voucher_number', 'desc')
            ->first();
        
        if ($lastVoucher) {
            // Extract the sequential number and increment
            $lastNumber = (int) substr($lastVoucher->voucher_number, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Status helpers
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'approved' => 'green',  
            'rejected' => 'red',
            'paid' => 'blue',
            default => 'gray',
        };
    }

    public function getPaymentMethodDisplayAttribute(): string
    {
        return match($this->payment_method) {
            'cash' => 'Cash',
            'cheque' => 'Cheque',
            'bank_transfer' => 'Bank Transfer',
            'online' => 'Online Payment',
            'card' => 'Card Payment',
            default => ucfirst($this->payment_method)
        };
    }

    /**
     * Approve voucher
     */
    public function approve(User $approver, string $notes = null): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);
    }

    /**
     * Reject voucher
     */
    public function reject(User $approver, string $notes = null): void
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);
    }

    /**
     * Mark as paid
     */
    public function markAsPaid(string $paymentDate = null): void
    {
        $this->update([
            'status' => 'paid',
            'payment_date' => $paymentDate ? Carbon::parse($paymentDate) : now(),
        ]);
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

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('expense_category', $category);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('voucher_date', [$startDate, $endDate]);
    }
}
