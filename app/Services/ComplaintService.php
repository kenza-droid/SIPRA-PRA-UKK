<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\ComplaintHistory;
use App\Models\ComplaintImage;
use App\Models\ComplaintProgress;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ComplaintService
{
    /**
     * Buat aspirasi baru beserta upload gambar.
     */
    public function create(User $user, array $data, array $images = []): Complaint
    {
        return DB::transaction(function () use ($user, $data, $images) {
            $complaint = Complaint::create([
                'complaint_number' => $this->generateNumber(),
                'user_id' => $user->id,
                'category_id' => $data['category_id'],
                'title' => $data['title'],
                'location' => $data['location'],
                'description' => $data['description'],
                'priority' => $data['priority'],
                'status' => 'menunggu',
                'progress_percentage' => 0,
            ]);

            // Upload gambar
            foreach ($images as $index => $file) {
                $this->storeImage($complaint, $file, $index);
            }

            // Catat histori
            $this->recordHistory($complaint, null, 'menunggu', 'create', 'Aspirasi dibuat oleh siswa.', $user->id);

            return $complaint;
        });
    }

    /**
     * Update status aspirasi dan catat histori.
     */
    public function updateStatus(Complaint $complaint, string $newStatus, User $admin, ?string $note = null): void
    {
        DB::transaction(function () use ($complaint, $newStatus, $admin, $note) {
            $prevStatus = $complaint->status;

            $updateData = ['status' => $newStatus];

            if ($newStatus === 'ditinjau' && ! $complaint->reviewed_at) {
                $updateData['reviewed_at'] = now();
            }
            if ($newStatus === 'selesai') {
                $updateData['resolved_at'] = now();
                $updateData['progress_percentage'] = 100;
            }

            $complaint->update($updateData);

            $this->recordHistory(
                $complaint,
                $prevStatus,
                $newStatus,
                'update_status',
                $note ?? 'Status diperbarui oleh admin.',
                $admin->id
            );
        });
    }

    /**
     * Tambah umpan balik dari admin.
     */
    public function addFeedback(Complaint $complaint, User $admin, array $data): Feedback
    {
        return DB::transaction(function () use ($complaint, $admin, $data) {
            $feedback = Feedback::create([
                'complaint_id' => $complaint->id,
                'admin_id' => $admin->id,
                'message' => $data['message'],
                'type' => $data['type'],
            ]);

            $this->recordHistory(
                $complaint,
                $complaint->status,
                $complaint->status,
                'add_feedback',
                'Admin memberikan umpan balik.',
                $admin->id
            );

            return $feedback;
        });
    }

    /**
     * Tambah progres perbaikan.
     */
    public function addProgress(
        Complaint $complaint,
        User $admin,
        array $data,
        ?UploadedFile $image = null
    ): ComplaintProgress {
        return DB::transaction(function () use ($complaint, $admin, $data, $image) {
            $imagePath = null;
            if ($image) {
                $imagePath = $image->store('complaint-progress', 'public');
            }

            $progress = ComplaintProgress::create([
                'complaint_id' => $complaint->id,
                'created_by' => $admin->id,
                'title' => $data['title'],
                'description' => $data['description'],
                'progress_percentage' => $data['progress_percentage'],
                'work_date' => $data['work_date'] ?? null,
                'image_path' => $imagePath,
            ]);

            // Sync persentase ke complaint
            $complaint->update(['progress_percentage' => $data['progress_percentage']]);

            $this->recordHistory(
                $complaint,
                $complaint->status,
                $complaint->status,
                'add_progress',
                "Progres perbaikan: {$data['progress_percentage']}% - {$data['title']}",
                $admin->id
            );

            return $progress;
        });
    }

    // ─── Private Helpers ──────────────────────────────────────

    private function generateNumber(): string
    {
        $year = now()->year;
        $count = Complaint::whereYear('created_at', $year)->count() + 1;

        return sprintf('SPR-%s-%04d', $year, $count);
    }

    private function storeImage(Complaint $complaint, UploadedFile $file, int $order): ComplaintImage
    {
        $path = $file->store('complaint-images', 'public');

        return ComplaintImage::create([
            'complaint_id' => $complaint->id,
            'image_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'sort_order' => $order,
        ]);
    }

    private function recordHistory(
        Complaint $complaint,
        ?string $previousStatus,
        ?string $newStatus,
        string $action,
        ?string $note,
        int $changedBy
    ): void {
        ComplaintHistory::create([
            'complaint_id' => $complaint->id,
            'previous_status' => $previousStatus,
            'new_status' => $newStatus,
            'action' => $action,
            'note' => $note,
            'changed_by' => $changedBy,
            'created_at' => now(),
        ]);
    }
}
