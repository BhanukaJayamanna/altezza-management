<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'icon',
        'color',
        'read_at',
        'action_url',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * Get the user that owns the notification
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope to get read notifications
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    /**
     * Check if notification is read
     */
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Get relative time ago
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Static method to create notifications easily
     */
    public static function createForUser($userId, $type, $title, $message, $data = null, $actionUrl = null)
    {
        $config = self::getTypeConfig($type);
        
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'icon' => $config['icon'],
            'color' => $config['color'],
            'action_url' => $actionUrl,
        ]);
    }

    /**
     * Get configuration for notification types
     */
    private static function getTypeConfig($type): array
    {
        $configs = [
            'payment' => ['icon' => 'dollar', 'color' => 'blue'],
            'maintenance' => ['icon' => 'warning', 'color' => 'amber'],
            'lease' => ['icon' => 'check', 'color' => 'green'],
            'overdue' => ['icon' => 'alert', 'color' => 'red'],
            'user_activity' => ['icon' => 'user', 'color' => 'purple'],
            'system' => ['icon' => 'info', 'color' => 'gray'],
        ];

        return $configs[$type] ?? ['icon' => 'bell', 'color' => 'blue'];
    }
}
