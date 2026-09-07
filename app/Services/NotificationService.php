<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Notifikasi ke semua admin saat ada aspirasi baru masuk.
     */
    public function notifyAdminsNewComplaint(Complaint $complaint): void
    {
        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            $this->create($admin->id, [
                'title' => 'Aspirasi Baru Masuk',
                'message' => "Aspirasi baru dari {$complaint->user->name}: \"{$complaint->title}\"",
                'type' => $complaint->priority === 'kritis' ? 'danger' : 'info',
                'icon' => 'inbox',
                'complaint_id' => $complaint->id,
                'action_url' => route('admin.complaints.show', $complaint),
            ]);
        }
    }

    /**
     * Notifikasi ke siswa saat status aspirasi diperbarui.
     */
    public function notifyStatusUpdated(Complaint $complaint): void
    {
        $statusLabel = Complaint::STATUSES[$complaint->status]['label'] ?? $complaint->status;

        $this->create($complaint->user_id, [
            'title' => 'Status Aspirasi Diperbarui',
            'message' => "Status aspirasi \"{$complaint->title}\" telah berubah menjadi {$statusLabel}.",
            'type' => $complaint->status === 'selesai' ? 'success'
                            : ($complaint->status === 'ditolak' ? 'danger' : 'info'),
            'icon' => 'arrow-path',
            'complaint_id' => $complaint->id,
            'action_url' => route('siswa.complaints.show', $complaint),
        ]);
    }

    /**
     * Notifikasi ke siswa saat admin memberikan umpan balik.
     */
    public function notifyFeedbackAdded(Complaint $complaint): void
    {
        $this->create($complaint->user_id, [
            'title' => 'Umpan Balik Baru',
            'message' => "Admin memberikan umpan balik pada aspirasi \"{$complaint->title}\".",
            'type' => 'info',
            'icon' => 'chat-bubble-left-right',
            'complaint_id' => $complaint->id,
            'action_url' => route('siswa.complaints.show', $complaint),
        ]);
    }

    /**
     * Notifikasi ke siswa saat progres ditambahkan.
     */
    public function notifyProgressAdded(Complaint $complaint): void
    {
        $this->create($complaint->user_id, [
            'title' => 'Progres Perbaikan Diperbarui',
            'message' => "Progres perbaikan aspirasi \"{$complaint->title}\" telah diperbarui menjadi {$complaint->progress_percentage}%.",
            'type' => 'success',
            'icon' => 'chart-bar',
            'complaint_id' => $complaint->id,
            'action_url' => route('siswa.complaints.show', $complaint),
        ]);
    }

    // ─── Private ─────────────────────────────────────────────

    private function create(int $userId, array $data): Notification
    {
        return Notification::create(array_merge(['user_id' => $userId], $data));
    }
}
