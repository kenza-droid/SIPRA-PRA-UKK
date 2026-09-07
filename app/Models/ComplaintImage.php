<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ComplaintImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'complaint_id',
        'image_path',
        'original_name',
        'mime_type',
        'file_size',
        'sort_order',
    ];

    // ─── Helpers ─────────────────────────────────────────────

    public function getUrlAttribute(): string
    {
        return Storage::url($this->image_path);
    }

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size ?? 0;
        if ($bytes < 1024) {
            return "{$bytes} B";
        }
        if ($bytes < 1048576) {
            return round($bytes / 1024, 1).' KB';
        }

        return round($bytes / 1048576, 1).' MB';
    }

    // ─── Relationships ────────────────────────────────────────

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }
}
