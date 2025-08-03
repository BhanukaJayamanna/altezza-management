<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notice extends Model
{
    protected $fillable = [
        'title',
        'content',
        'type',
        'priority',
        'is_urgent',
        'created_by',
        'published_at',
    ];

    protected $casts = [
        'is_urgent' => 'boolean',
        'published_at' => 'datetime',
    ];

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Status helpers
    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'general' => 'blue',
            'maintenance' => 'yellow',
            'emergency' => 'red',
            'policy' => 'purple',
            default => 'gray',
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'red',
            default => 'gray',
        };
    }
}
