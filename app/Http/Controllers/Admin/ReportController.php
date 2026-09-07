<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Complaint::with(['user', 'category']);

        // ── Filter ────────────────────────────────────────
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
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }
        if ($request->filled('siswa')) {
            $query->where('user_id', $request->siswa);
        }

        $complaints = $query->orderByDesc('created_at')->get();

        // ── Summary Stats ─────────────────────────────────
        $total = $complaints->count();
        $selesai = $complaints->where('status', 'selesai')->count();
        $completionRate = $total > 0 ? round(($selesai / $total) * 100, 1) : 0;

        // Rata-rata waktu penyelesaian (dalam hari)
        $avgResolutionDays = $complaints->where('status', 'selesai')
            ->filter(fn ($c) => $c->resolved_at)
            ->avg(fn ($c) => $c->created_at->diffInDays($c->resolved_at));

        // Per kategori
        $perKategori = $complaints->groupBy('category.name')
            ->map(fn ($group) => $group->count())
            ->sortDesc();

        // Per status
        $perStatus = $complaints->groupBy('status')
            ->map(fn ($group) => $group->count());

        // Per prioritas
        $perPrioritas = $complaints->groupBy('priority')
            ->map(fn ($group) => $group->count());

        // Monthly trend (filtered)
        $monthlyTrend = $complaints->groupBy(fn ($c) => $c->created_at->format('Y-m'))
            ->map(fn ($group) => $group->count())
            ->sortKeys();

        $categories = Category::active()->get();
        $siswaList = User::where('role', 'siswa')->orderBy('name')->get(['id', 'name', 'class']);

        return view('admin.laporan', compact(
            'complaints',
            'total',
            'selesai',
            'completionRate',
            'avgResolutionDays',
            'perKategori',
            'perStatus',
            'perPrioritas',
            'monthlyTrend',
            'categories',
            'siswaList',
        ));
    }
}
