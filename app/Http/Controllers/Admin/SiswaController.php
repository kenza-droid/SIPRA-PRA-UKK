<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
                    ->orWhere('class', 'like', '%'.$request->search.'%')
                    ->orWhere('phone', 'like', '%'.$request->search.'%');
            });
        }

        $siswaList = $query->paginate(20)->withQueryString();

        return view('admin.siswa', compact('siswaList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nisn' => 'required|string|max:20|unique:users,nisn',
            'class' => 'required|string|max:50',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Nama lengkap siswa wajib diisi.',
            'name.max' => 'Nama siswa maksimal 255 karakter.',
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.max' => 'NISN maksimal 20 karakter.',
            'nisn.unique' => 'NISN ini sudah digunakan oleh akun lain.',
            'class.required' => 'Kelas / jurusan siswa wajib diisi.',
            'class.max' => 'Keterangan kelas maksimal 50 karakter.',
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'email.required' => 'Email siswa wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'nisn' => $validated['nisn'],
            'class' => $validated['class'],
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'siswa',
        ]);

        return redirect()
            ->route('admin.siswa')
            ->with('success', "Akun siswa {$user->name} (NISN: {$user->nisn}) berhasil ditambahkan.");
    }
}
