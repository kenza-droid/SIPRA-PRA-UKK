<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Complaint;
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
     * Riwayat / list aspirasi siswa
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Complaint::forUser($user->id)
            ->with('category')
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
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $complaints = $query->paginate(10)->withQueryString();
        $categories = Category::active()->get();

        return view('siswa.histori', compact('complaints', 'categories'));
    }

    /**
     * Form buat aspirasi baru
     */
    public function create()
    {
        $categories = Category::active()->get();

        return view('siswa.form_aspirasi', compact('categories'));
    }

    /**
     * Simpan aspirasi baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'location' => 'required|string|max:200',
            'description' => 'required|string|min:20|max:2000',
            'priority' => 'required|in:rendah,sedang,tinggi,kritis',
            'phone' => 'nullable|string|max:20',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:3072',
        ], [
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'title.required' => 'Judul pengaduan belum diisi. Tulis ringkasannya dulu, ya.',
            'title.string' => 'Judul pengaduan harus berupa teks.',
            'title.max' => 'Judulnya kepanjangan. Maksimal 200 karakter, ya.',
            'category_id.required' => 'Pilih kategori fasilitas yang mau kamu laporkan.',
            'category_id.exists' => 'Kategori yang dipilih tidak ditemukan. Coba pilih lagi.',
            'location.required' => 'Lokasinya belum diisi. Tulis nama ruangan, gedung, atau areanya.',
            'location.string' => 'Lokasi harus berupa teks.',
            'location.max' => 'Keterangan lokasi maksimal 200 karakter.',
            'description.required' => 'Ceritakan dulu masalahnya di kolom deskripsi.',
            'description.string' => 'Deskripsi masalah harus berupa teks.',
            'description.min' => 'Penjelasannya masih terlalu singkat. Tulis minimal 20 karakter biar petugas paham kondisinya.',
            'description.max' => 'Deskripsi maksimal 2.000 karakter.',
            'priority.required' => 'Pilih seberapa mendesak masalah ini untuk ditangani.',
            'priority.in' => 'Pilihan prioritas tidak valid. Silakan pilih dari daftar yang ada.',
            'images.array' => 'Format unggahan foto tidak valid.',
            'images.max' => 'Maksimal hanya bisa melampirkan 5 foto.',
            'images.*.image' => 'File yang dipilih harus berupa gambar.',
            'images.*.mimes' => 'Format foto harus JPG, JPEG, PNG, atau WebP.',
            'images.*.max' => 'Ukuran tiap foto maksimal 3 MB, ya.',
        ]);

        if (array_key_exists('phone', $validated) && $validated['phone'] !== Auth::user()->phone) {
            Auth::user()->update(['phone' => $validated['phone']]);
        }

        $complaint = $this->complaintService->create(
            Auth::user(),
            $validated,
            $request->file('images') ?? []
        );

        // Kirim notifikasi ke admin
        $this->notificationService->notifyAdminsNewComplaint($complaint);

        return redirect()
            ->route('siswa.complaints.show', $complaint)
            ->with('success', 'Pengaduanmu berhasil dikirim (Nomor: '.$complaint->complaint_number.'). Petugas akan segera memeriksanya.');
    }

    /**
     * Detail aspirasi siswa
     */
    public function show(Complaint $complaint)
    {
        // Pastikan hanya pemilik yang bisa akses
        abort_if($complaint->user_id !== Auth::id(), 403);

        $complaint->load([
            'category',
            'images',
            'feedback.admin',
            'progress.creator',
            'history.changedBy',
        ]);

        return view('siswa.detail_aspirasi', compact('complaint'));
    }

    /**
     * Hapus riwayat pengaduan terpilih (bulk delete).
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:complaints,id',
        ], [
            'ids.required' => 'Pilih minimal satu pengaduan untuk dihapus.',
            'ids.min' => 'Pilih minimal satu pengaduan untuk dihapus.',
            'ids.*.exists' => 'Data pengaduan yang dipilih tidak valid.',
        ]);

        $userId = Auth::id();
        $ids = $validated['ids'];

        // Keamanan 1: Validasi kepemilikan ketat (hanya pengaduan milik siswa yang login)
        $complaints = Complaint::where('user_id', $userId)
            ->whereIn('id', $ids)
            ->with('images')
            ->get();

        if ($complaints->isEmpty()) {
            return redirect()
                ->route('siswa.complaints.index')
                ->with('error', 'Tidak ada pengaduan valid milik Anda yang dapat dihapus.');
        }

        // Keamanan 2: Batasan status (hanya status menunggu, selesai, atau ditolak yang boleh dihapus)
        // Pengaduan yang sedang diproses atau dalam perbaikan aktif tidak dapat dihapus sembarangan
        $allowedStatuses = ['menunggu', 'selesai', 'ditolak'];
        $nonDeletable = $complaints->filter(fn ($c) => ! in_array($c->status, $allowedStatuses));

        if ($nonDeletable->isNotEmpty()) {
            return redirect()
                ->route('siswa.complaints.index')
                ->with('error', 'Pengaduan yang sedang ditinjau, diproses, atau dalam perbaikan tidak dapat dihapus.');
        }

        $deletedCount = $this->complaintService->bulkDelete($complaints);

        return redirect()
            ->route('siswa.complaints.index')
            ->with('success', "{$deletedCount} riwayat aspirasi berhasil dihapus.");
    }

    /**
     * Hapus satu riwayat pengaduan.
     */
    public function destroy(Complaint $complaint)
    {
        // Validasi kepemilikan ketat
        abort_if($complaint->user_id !== Auth::id(), 403);

        $allowedStatuses = ['menunggu', 'selesai', 'ditolak'];
        if (! in_array($complaint->status, $allowedStatuses)) {
            return redirect()
                ->route('siswa.complaints.index')
                ->with('error', 'Pengaduan yang sedang diproses tidak dapat dihapus.');
        }

        $this->complaintService->bulkDelete(collect([$complaint]));

        return redirect()
            ->route('siswa.complaints.index')
            ->with('success', 'Pengaduan berhasil dihapus.');
    }
}
