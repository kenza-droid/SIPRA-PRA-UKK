<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ComplaintController as AdminComplaintController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Siswa\ComplaintController as SiswaComplaintController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\ProfileController as SiswaProfileController;
use Illuminate\Support\Facades\Route;

// ── Root Redirect ────────────────────────────────────────────
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('siswa.dashboard');
    }

    return redirect()->route('login');
});

// ── Auth ─────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ── Siswa Area ───────────────────────────────────────────────
Route::middleware(['auth', 'role:siswa'])
    ->prefix('siswa')
    ->name('siswa.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');

        // Aspirasi (complaints)
        Route::prefix('aspirasi')->name('complaints.')->group(function () {
            Route::get('/', [SiswaComplaintController::class, 'index'])->name('index');
            Route::get('/buat', [SiswaComplaintController::class, 'create'])->name('create');
            Route::post('/', [SiswaComplaintController::class, 'store'])->name('store');
            Route::get('/{complaint}', [SiswaComplaintController::class, 'show'])->name('show');
        });

        // Profil
        Route::prefix('profil')->name('profile.')->group(function () {
            Route::get('/', [SiswaProfileController::class, 'show'])->name('show');
            Route::put('/', [SiswaProfileController::class, 'update'])->name('update');
            Route::put('/ganti-password', [SiswaProfileController::class, 'updatePassword'])->name('password');
        });

        // Notifikasi
        Route::prefix('notifikasi')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::post('/baca-semua', [NotificationController::class, 'markAllRead'])->name('read-all');
            Route::post('/{notification}/baca', [NotificationController::class, 'markRead'])->name('read');
        });
    });

// ── Admin Area ───────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Manajemen Aspirasi
        Route::prefix('aspirasi')->name('complaints.')->group(function () {
            Route::get('/', [AdminComplaintController::class, 'index'])->name('index');
            Route::get('/{complaint}', [AdminComplaintController::class, 'show'])->name('show');
            Route::post('/{complaint}/action', [AdminComplaintController::class, 'processAction'])->name('action');
            Route::put('/{complaint}/status', [AdminComplaintController::class, 'updateStatus'])->name('status');
            Route::post('/{complaint}/feedback', [AdminComplaintController::class, 'addFeedback'])->name('feedback');
            Route::post('/{complaint}/progres', [AdminComplaintController::class, 'addProgress'])->name('progress');
        });

        // Kategori
        Route::resource('kategori', CategoryController::class)
            ->except(['create', 'edit', 'show'])
            ->names('categories');

        // Laporan
        Route::get('/laporan', [ReportController::class, 'index'])->name('report');

        // Data Siswa
        Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa');

        // Notifikasi (admin juga pakai notif)
        Route::prefix('notifikasi')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::post('/baca-semua', [NotificationController::class, 'markAllRead'])->name('read-all');
            Route::post('/{notification}/baca', [NotificationController::class, 'markRead'])->name('read');
        });
    });

// ── API-style JSON endpoints (auth) ──────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/api/notifikasi/count', [NotificationController::class, 'getUnreadCount'])->name('api.notifications.count');
    Route::get('/api/notifikasi', [NotificationController::class, 'getDropdown'])->name('api.notifications.dropdown');
});
