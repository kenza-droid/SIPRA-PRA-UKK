<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
            'role' => 'required|in:siswa,admin',
        ], [
            'login.required' => 'Username, email, atau NISN wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
            'role.required' => 'Pilih role terlebih dahulu.',
        ]);

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        // Try NISN login for siswa
        if ($request->role === 'siswa' && is_numeric($request->login)) {
            $loginField = 'nisn';
        }

        $user = User::where($loginField, $request->login)
            ->where('role', $request->role)
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => 'Kredensial yang Anda masukkan tidak sesuai.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended($this->defaultRouteByRole($user));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil keluar.');
    }

    private function redirectByRole(User $user)
    {
        return redirect($this->defaultRouteByRole($user));
    }

    private function defaultRouteByRole(User $user): string
    {
        return $user->isAdmin()
            ? route('admin.dashboard')
            : route('siswa.dashboard');
    }
}
