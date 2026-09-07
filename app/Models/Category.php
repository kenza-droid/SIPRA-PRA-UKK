<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ─── Scopes ───────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    // ─── Helpers ─────────────────────────────────────────────

    /**
     * Return a Tailwind-safe color class for the category badge.
     */
    public function getColorClassAttribute(): string
    {
        return match ($this->color) {
            'blue' => 'bg-blue-100 text-blue-700',
            'green' => 'bg-emerald-100 text-emerald-700',
            'yellow' => 'bg-yellow-100 text-yellow-700',
            'red' => 'bg-red-100 text-red-700',
            'purple' => 'bg-purple-100 text-purple-700',
            'pink' => 'bg-pink-100 text-pink-700',
            'orange' => 'bg-orange-100 text-orange-700',
            'teal' => 'bg-teal-100 text-teal-700',
            'cyan' => 'bg-cyan-100 text-cyan-700',
            default => 'bg-indigo-100 text-indigo-700',
        };
    }

    // ─── Relationships ────────────────────────────────────────

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class);
    }
}
