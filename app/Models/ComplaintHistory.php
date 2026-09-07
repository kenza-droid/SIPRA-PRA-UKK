<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintHistory extends Model
{
    protected $table = 'complaint_history';

    public $timestamps = false;

    protected $fillable = [
        'complaint_id',
        'previous_status',
        'new_status',
        'action',
        'note',
        'changed_by',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    // ─── Helpers ─────────────────────────────────────────────

    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'create' => 'Aspirasi dibuat',
            'update_status' => 'Status diperbarui',
            'add_feedback' => 'Umpan balik diberikan',
            'add_progress' => 'Progres ditambahkan',
            'assign' => 'Ditugaskan ke petugas',
            default => $this->action ?? 'Aktivitas',
        };
    }

    public function getActionIconAttribute(): string
    {
        return match ($this->action) {
            'create' => 'plus-circle',
            'update_status' => 'arrow-path',
            'add_feedback' => 'chat-bubble-left-right',
            'add_progress' => 'chart-bar',
            'assign' => 'user-plus',
            default => 'clock',
        };
    }

    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'create' => 'indigo',
            'update_status' => 'blue',
            'add_feedback' => 'purple',
            'add_progress' => 'emerald',
            'assign' => 'amber',
            default => 'gray',
        };
    }

    // ─── Relationships ────────────────────────────────────────

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
