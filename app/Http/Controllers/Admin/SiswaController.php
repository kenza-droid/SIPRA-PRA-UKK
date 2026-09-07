<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'siswa')
            ->withCount(['complaints', 'complaints as selesai_count' => fn ($q) => $q->where('status', 'selesai')])
            ->orderBy('name');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('nisn', 'like', '%'.$request->search.'%')
                    ->orWhere('class', 'like', '%'.$request->search.'%');
            });
        }

        $siswaList = $query->paginate(20)->withQueryString();

        return view('admin.siswa', compact('siswaList'));
    }
}
