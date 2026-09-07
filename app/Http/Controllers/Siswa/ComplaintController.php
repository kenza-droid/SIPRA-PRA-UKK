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
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:3072',
        ], [
            'title.required' => 'Judul aspirasi wajib diisi.',
            'category_id.required' => 'Pilih kategori aspirasi.',
            'location.required' => 'Lokasi wajib diisi.',
            'description.required' => 'Deskripsi wajib diisi.',
            'description.min' => 'Deskripsi minimal 20 karakter.',
            'priority.required' => 'Pilih tingkat prioritas.',
            'images.*.image' => 'File harus berupa gambar.',
            'images.*.max' => 'Ukuran foto maksimal 3 MB.',
        ]);

        $complaint = $this->complaintService->create(
            Auth::user(),
            $validated,
            $request->file('images') ?? []
        );

        // Kirim notifikasi ke admin
        $this->notificationService->notifyAdminsNewComplaint($complaint);

        return redirect()
            ->route('siswa.complaints.show', $complaint)
            ->with('success', 'Aspirasi berhasil dikirim! Nomor: '.$complaint->complaint_number);
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
}
