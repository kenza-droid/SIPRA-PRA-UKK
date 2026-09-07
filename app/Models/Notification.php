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
        'title',
        'message',
        'type',
        'icon',
        'complaint_id',
        'action_url',
        'is_read',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
        ];
    }

    // ─── Scopes ───────────────────────────────────────────────

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ─── Helpers ─────────────────────────────────────────────

    public function markAsRead(): void
    {
        $this->update(['is_read' => true, 'read_at' => now()]);
    }

    public function getIconClassAttribute(): string
    {
        return match ($this->type) {
            'success' => 'text-emerald-500',
            'warning' => 'text-amber-500',
            'danger' => 'text-red-500',
            default => 'text-blue-500',
        };
    }

    public function getBgClassAttribute(): string
    {
        return match ($this->type) {
            'success' => 'bg-emerald-50 border-emerald-100',
            'warning' => 'bg-amber-50 border-amber-100',
            'danger' => 'bg-red-50 border-red-100',
            default => 'bg-blue-50 border-blue-100',
        };
    }

    // ─── Relationships ────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }
}
