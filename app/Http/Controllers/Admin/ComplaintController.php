<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\User;
use App\Services\ComplaintService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    public function __construct(
        private ComplaintService $complaintService,
        private NotificationService $notificationService,
    ) {}

    /**
     * Daftar semua aspirasi
     */
    public function index(Request $request)
    {
        $query = Complaint::with(['user', 'category'])
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $query->search($request->search);
        }
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }
        if ($request->filled('priority')) {
            $query->byPriority($request->priority);
        }
        if ($request->filled('siswa')) {
            $query->where('user_id', $request->siswa);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $allowedSorts = ['created_at', 'title', 'status', 'priority'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        }

        $complaints = $query->paginate(15)->withQueryString();
        $categories = Category::active()->get();
        $siswaList = User::where('role', 'siswa')->orderBy('name')->get(['id', 'name', 'class']);

        return view('admin.complaints.index', compact('complaints', 'categories', 'siswaList'));
    }

    /**
     * Detail & management panel aspirasi
     */
    public function show(Complaint $complaint)
    {
        $complaint->load([
            'user',
            'category',
            'images',
            'feedback.admin',
            'progress.creator',
            'history.changedBy',
        ]);

        return view('admin.complaints.show', compact('complaint'));
    }

    /**
     * Proses tindak lanjut terpadu (Update Status + Progres + Umpan Balik dalam 1 aksi)
     */
    public function processAction(Request $request, Complaint $complaint)
    {
        $request->validate([
            'status' => 'required|in:'.implode(',', array_keys(Complaint::STATUSES)),
            'progress_percentage' => 'nullable|integer|min:0|max:100',
            'message' => 'nullable|string|max:1000',
            'feedback_type' => 'nullable|in:informasi,tindakan,penolakan,selesai',
            'title' => 'nullable|string|max:200',
            'work_date' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ], [
            'status.required' => 'Pilih status aspirasi.',
            'image.max' => 'Ukuran foto maksimal 3MB.',
            'image.image' => 'File harus berupa gambar.',
        ]);

        $newStatus = $request->status;
        $prevStatus = $complaint->status;
        $statusChanged = $newStatus !== $prevStatus;

        $progressPct = $request->filled('progress_percentage')
            ? (int) $request->progress_percentage
            : ($newStatus === 'selesai' ? 100 : $complaint->progress_percentage);

        if ($newStatus === 'selesai') {
            $progressPct = 100;
        }

        $note = $request->filled('message') ? $request->message : null;

        // 1. Update Status (jika status berubah atau jika ada catatan)
        if ($statusChanged || $note) {
            $this->complaintService->updateStatus(
                $complaint,
                $newStatus,
                Auth::user(),
                $note
            );
            $this->notificationService->notifyStatusUpdated($complaint);
        }

        // 2. Tambah Umpan Balik (jika pesan diisi)
        if ($request->filled('message')) {
            $feedbackType = $request->feedback_type ?: match($newStatus) {
                'ditolak' => 'penolakan',
                'selesai' => 'selesai',
                'diproses', 'dalam_perbaikan' => 'tindakan',
                default => 'informasi',
            };

            $this->complaintService->addFeedback($complaint, Auth::user(), [
                'message' => $request->message,
                'type' => $feedbackType,
            ]);
            $this->notificationService->notifyFeedbackAdded($complaint);
        }

        // 3. Tambah Progres Perbaikan (jika persentase berubah, atau ada foto, atau ada judul progres)
        $hasProgressUpdate = $request->hasFile('image')
            || $request->filled('title')
            || ($progressPct !== $complaint->progress_percentage)
            || ($statusChanged && in_array($newStatus, ['dalam_perbaikan', 'selesai']));

        if ($hasProgressUpdate) {
            $progressTitle = $request->filled('title')
                ? $request->title
                : 'Status: ' . Complaint::STATUSES[$newStatus]['label'];

            $progressDesc = $request->filled('message')
                ? $request->message
                : 'Pembaruan progres penanganan menjadi ' . $progressPct . '%.';

            $this->complaintService->addProgress(
                $complaint,
                Auth::user(),
                [
                    'title' => $progressTitle,
                    'description' => $progressDesc,
                    'progress_percentage' => $progressPct,
                    'work_date' => $request->work_date ?: now()->format('Y-m-d'),
                ],
                $request->file('image')
            );
            $this->notificationService->notifyProgressAdded($complaint);
        }

        return back()->with('success', 'Tindak lanjut berhasil disimpan. Status, progres, dan umpan balik diperbarui sekaligus.');
    }

    /**
     * Update status aspirasi
     */
    public function updateStatus(Request $request, Complaint $complaint)
    {
        $request->validate([
            'status' => 'required|in:'.implode(',', array_keys(Complaint::STATUSES)),
            'note' => 'nullable|string|max:500',
        ]);

        $this->complaintService->updateStatus(
            $complaint,
            $request->status,
            Auth::user(),
            $request->note
        );

        $this->notificationService->notifyStatusUpdated($complaint);

        return back()->with('success', 'Status berhasil diperbarui menjadi "'.Complaint::STATUSES[$request->status]['label'].'".');
    }

    /**
     * Tambah umpan balik
     */
    public function addFeedback(Request $request, Complaint $complaint)
    {
        $request->validate([
            'message' => 'required|string|min:5|max:1000',
            'type' => 'required|in:informasi,tindakan,penolakan,selesai',
        ], [
            'message.required' => 'Isi umpan balik wajib diisi.',
            'message.min' => 'Umpan balik minimal 5 karakter.',
        ]);

        $this->complaintService->addFeedback($complaint, Auth::user(), $request->only(['message', 'type']));
        $this->notificationService->notifyFeedbackAdded($complaint);

        return back()->with('success', 'Umpan balik berhasil dikirim.');
    }

    /**
     * Tambah progres perbaikan
     */
    public function addProgress(Request $request, Complaint $complaint)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'required|string|max:1000',
            'progress_percentage' => 'required|integer|min:0|max:100',
            'work_date' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ], [
            'title.required' => 'Judul progres wajib diisi.',
            'description.required' => 'Deskripsi progres wajib diisi.',
            'progress_percentage.required' => 'Persentase progres wajib diisi.',
        ]);

        $this->complaintService->addProgress(
            $complaint,
            Auth::user(),
            $request->only(['title', 'description', 'progress_percentage', 'work_date']),
            $request->file('image')
        );

        $this->notificationService->notifyProgressAdded($complaint);

        return back()->with('success', 'Progres perbaikan berhasil ditambahkan.');
    }
}
