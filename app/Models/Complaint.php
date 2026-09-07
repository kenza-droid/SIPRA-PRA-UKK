<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'complaint_number',
        'user_id',
        'category_id',
        'title',
        'location',
        'description',
        'priority',
        'status',
        'progress_percentage',
        'reviewed_at',
        'resolved_at',
        'assigned_to',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    // ─── Status Config ────────────────────────────────────────

    public const STATUSES = [
        'menunggu' => ['label' => 'Menunggu',        'color' => 'amber',   'icon' => 'clock'],
        'ditinjau' => ['label' => 'Ditinjau',        'color' => 'blue',    'icon' => 'eye'],
        'diproses' => ['label' => 'Diproses',        'color' => 'purple',  'icon' => 'cog'],
        'dalam_perbaikan' => ['label' => 'Dalam Perbaikan', 'color' => 'orange',  'icon' => 'wrench'],
        'selesai' => ['label' => 'Selesai',         'color' => 'green',   'icon' => 'check-circle'],
        'ditolak' => ['label' => 'Ditolak',         'color' => 'red',     'icon' => 'x-circle'],
    ];

    public const PRIORITIES = [
        'rendah' => ['label' => 'Rendah',  'color' => 'gray'],
        'sedang' => ['label' => 'Sedang',  'color' => 'blue'],
        'tinggi' => ['label' => 'Tinggi',  'color' => 'orange'],
        'kritis' => ['label' => 'Kritis',  'color' => 'red'],
    ];

    // ─── Scopes ───────────────────────────────────────────────

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeSearch($query, string $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('title', 'like', "%{$keyword}%")
                ->orWhere('description', 'like', "%{$keyword}%")
                ->orWhere('location', 'like', "%{$keyword}%")
                ->orWhere('complaint_number', 'like', "%{$keyword}%");
        });
    }

    public function scopeNeedingAttention($query)
    {
        return $query->whereIn('status', ['menunggu', 'ditinjau'])
            ->orWhere('priority', 'kritis');
    }

    // ─── Helpers ─────────────────────────────────────────────

    public function getStatusConfigAttribute(): array
    {
        return self::STATUSES[$this->status] ?? ['label' => $this->status, 'color' => 'gray', 'icon' => 'circle'];
    }

    public function getPriorityConfigAttribute(): array
    {
        return self::PRIORITIES[$this->priority] ?? ['label' => $this->priority, 'color' => 'gray'];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status]['label'] ?? $this->status;
    }

    public function getPriorityLabelAttribute(): string
    {
        return self::PRIORITIES[$this->priority]['label'] ?? $this->priority;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'menunggu' => 'bg-amber-100 text-amber-700 ring-amber-200',
            'ditinjau' => 'bg-blue-100 text-blue-700 ring-blue-200',
            'diproses' => 'bg-purple-100 text-purple-700 ring-purple-200',
            'dalam_perbaikan' => 'bg-orange-100 text-orange-700 ring-orange-200',
            'selesai' => 'bg-emerald-100 text-emerald-700 ring-emerald-200',
            'ditolak' => 'bg-red-100 text-red-700 ring-red-200',
            default => 'bg-gray-100 text-gray-700 ring-gray-200',
        };
    }

    public function getPriorityBadgeClassAttribute(): string
    {
        return match ($this->priority) {
            'rendah' => 'bg-gray-100 text-gray-600',
            'sedang' => 'bg-blue-100 text-blue-600',
            'tinggi' => 'bg-orange-100 text-orange-600',
            'kritis' => 'bg-red-100 text-red-600',
            default => 'bg-gray-100 text-gray-600',
        };
    }

    public function isCompleted(): bool
    {
        return $this->status === 'selesai';
    }

    public function isRejected(): bool
    {
        return $this->status === 'ditolak';
    }

    public function getDaysOpenAttribute(): int
    {
        return $this->created_at->diffInDays(now());
    }

    public function getProgressStepsAttribute(): array
    {
        $steps = ['menunggu', 'ditinjau', 'diproses', 'dalam_perbaikan', 'selesai'];
        $currentIndex = array_search($this->status, $steps);

        return collect($steps)->map(function ($step, $index) use ($currentIndex) {
            return [
                'key' => $step,
                'label' => self::STATUSES[$step]['label'],
                'icon' => self::STATUSES[$step]['icon'],
                'state' => $index < $currentIndex ? 'done'
                             : ($index === $currentIndex ? 'active' : 'pending'),
            ];
        })->toArray();
    }

    // ─── Relationships ────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ComplaintImage::class)->orderBy('sort_order');
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class)->orderByDesc('created_at');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(ComplaintProgress::class)->orderByDesc('created_at');
    }

    public function history(): HasMany
    {
        return $this->hasMany(ComplaintHistory::class)->orderByDesc('created_at');
    }

    public function latestProgress(): HasMany
    {
        return $this->hasMany(ComplaintProgress::class)->orderByDesc('created_at')->limit(1);
    }
}
