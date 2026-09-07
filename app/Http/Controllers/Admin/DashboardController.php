<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Stat Cards ──────────────────────────────────────
        $stats = [
            'total' => Complaint::count(),
            'menunggu' => Complaint::byStatus('menunggu')->count(),
            'diproses' => Complaint::whereIn('status', ['ditinjau', 'diproses'])->count(),
            'dalam_perbaikan' => Complaint::byStatus('dalam_perbaikan')->count(),
            'selesai_bulan' => Complaint::byStatus('selesai')
                ->whereMonth('resolved_at', now()->month)
                ->whereYear('resolved_at', now()->year)
                ->count(),
            'total_siswa' => User::where('role', 'siswa')->count(),
        ];

        // ── Chart: Aspirasi per Bulan (6 bulan terakhir) ────
        $monthlyData = Complaint::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(fn ($r) => [
            'label' => $this->monthLabel($r->month, $r->year),
            'total' => $r->total,
        ]);

        // ── Chart: Per Kategori ──────────────────────────────
        $categoryData = Complaint::select('category_id', DB::raw('COUNT(*) as total'))
            ->with('category:id,name,color')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->get();

        // ── Chart: Status Distribution ───────────────────────
        $statusData = Complaint::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn ($r) => [$r->status => $r->total]);

        // ── Aspirasi butuh perhatian ─────────────────────────
        $needAttention = Complaint::with(['user', 'category'])
            ->where(function ($q) {
                $q->where('status', 'menunggu')
                    ->orWhere('priority', 'kritis')
                    ->orWhere(function ($q2) {
                        $q2->whereNotIn('status', ['selesai', 'ditolak'])
                            ->where('created_at', '<=', now()->subDays(7));
                    });
            })
            ->orderByRaw("FIELD(priority,'kritis','tinggi','sedang','rendah')")
            ->orderBy('created_at')
            ->limit(8)
            ->get();

        // ── Aktivitas Terbaru ────────────────────────────────
        $recentActivity = ComplaintHistory::with(['complaint', 'changedBy'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'monthlyData',
            'categoryData',
            'statusData',
            'needAttention',
            'recentActivity'
        ));
    }

    private function monthLabel(int $month, int $year): string
    {
        $months = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        return $months[$month].' '.$year;
    }
}
