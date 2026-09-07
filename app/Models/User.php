<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'nisn',
        'class',
        'password',
        'role',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ─── Helpers ─────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/'.$this->avatar);
        }
        // Generate initials-based avatar URL
        $initials = collect(explode(' ', $this->name))
            ->take(2)
            ->map(fn ($w) => strtoupper(substr($w, 0, 1)))
            ->implode('');

        return 'https://ui-avatars.com/api/?name='.urlencode($initials)
            .'&background=4f46e5&color=fff&size=128&bold=true';
    }

    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->notifications()->where('is_read', false)->count();
    }

    // ─── Relationships ────────────────────────────────────────

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class);
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class, 'admin_id');
    }

    public function complaintHistory(): HasMany
    {
        return $this->hasMany(ComplaintHistory::class, 'changed_by');
    }

    public function complaintProgress(): HasMany
    {
        return $this->hasMany(ComplaintProgress::class, 'created_by');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->orderByDesc('created_at');
    }

    public function assignedComplaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'assigned_to');
    }
}
