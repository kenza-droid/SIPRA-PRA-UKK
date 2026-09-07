<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'complaint_id',
        'admin_id',
        'message',
        'type',
    ];

    public const TYPES = [
        'informasi' => ['label' => 'Informasi',  'color' => 'blue',  'icon' => 'information-circle'],
        'tindakan' => ['label' => 'Tindakan',   'color' => 'green', 'icon' => 'check-badge'],
        'penolakan' => ['label' => 'Penolakan',  'color' => 'red',   'icon' => 'x-circle'],
        'selesai' => ['label' => 'Selesai',    'color' => 'emerald', 'icon' => 'check-circle'],
    ];

    // ─── Helpers ─────────────────────────────────────────────

    public function getTypeConfigAttribute(): array
    {
        return self::TYPES[$this->type] ?? ['label' => $this->type, 'color' => 'gray', 'icon' => 'chat-bubble-left'];
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->type) {
            'informasi' => 'bg-blue-100 text-blue-700',
            'tindakan' => 'bg-green-100 text-green-700',
            'penolakan' => 'bg-red-100 text-red-700',
            'selesai' => 'bg-emerald-100 text-emerald-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    // ─── Relationships ────────────────────────────────────────

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
