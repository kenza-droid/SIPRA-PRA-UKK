<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ComplaintProgress extends Model
{
    use HasFactory;

    protected $table = 'complaint_progress';

    protected $fillable = [
        'complaint_id',
        'created_by',
        'title',
        'description',
        'progress_percentage',
        'image_path',
        'work_date',
    ];

    protected function casts(): array
    {
        return [
            'work_date' => 'date',
        ];
    }

    // ─── Helpers ─────────────────────────────────────────────

    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image_path)) {
            return null;
        }

        if (filter_var($this->image_path, FILTER_VALIDATE_URL)) {
            return $this->image_path;
        }

        $path = ltrim($this->image_path, '/');
        $path = preg_replace('#^(public/|storage/)+#', '', $path);

        return '/storage/' . $path;
    }

    public function getProgressBarColorAttribute(): string
    {
        return match (true) {
            $this->progress_percentage >= 100 => 'bg-emerald-500',
            $this->progress_percentage >= 75 => 'bg-blue-500',
            $this->progress_percentage >= 50 => 'bg-indigo-500',
            $this->progress_percentage >= 25 => 'bg-amber-500',
            default => 'bg-gray-400',
        };
    }

    // ─── Relationships ────────────────────────────────────────

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
