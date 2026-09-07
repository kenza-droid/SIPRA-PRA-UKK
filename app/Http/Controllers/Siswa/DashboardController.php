<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'total' => Complaint::forUser($user->id)->count(),
            'menunggu' => Complaint::forUser($user->id)->byStatus('menunggu')->count(),
            'diproses' => Complaint::forUser($user->id)->whereIn('status', ['ditinjau', 'diproses'])->count(),
            'dalam_perbaikan' => Complaint::forUser($user->id)->byStatus('dalam_perbaikan')->count(),
            'selesai' => Complaint::forUser($user->id)->byStatus('selesai')->count(),
        ];

        $recentComplaints = Complaint::forUser($user->id)
            ->with(['category', 'progress' => fn ($q) => $q->latest()->limit(1)])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $activeComplaint = Complaint::forUser($user->id)
            ->whereNotIn('status', ['selesai', 'ditolak'])
            ->with(['history' => fn ($q) => $q->orderBy('created_at')])
            ->orderByDesc('updated_at')
            ->first();

        return view('siswa.dashboard', compact('stats', 'recentComplaints', 'activeComplaint'));
    }
}
