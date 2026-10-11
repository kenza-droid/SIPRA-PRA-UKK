# SIPRA — Sistem Informasi Pengaduan Sarana dan Prasarana

> **"Laporkan. Pantau. Perbaiki."**
>
> Satu platform untuk menyampaikan aspirasi dan memantau proses perbaikan sarana sekolah secara transparan.

---

## Daftar Isi

- [Tentang Aplikasi](#tentang-aplikasi)
- [Fitur Utama](#fitur-utama)
- [Stack Teknologi](#stack-teknologi)
- [Struktur Proyek](#struktur-proyek)
- [Panduan Setup](#panduan-setup)
- [Akun Demo](#akun-demo)
- [Alur Penggunaan](#alur-penggunaan)
- [Skema Database](#skema-database)
- [Desain Sistem](#desain-sistem)

---

## Tentang Aplikasi

SIPRA adalah platform digital berbasis web yang membantu siswa menyampaikan pengaduan, keluhan, dan aspirasi terkait sarana serta prasarana sekolah. Sistem ini juga membantu Admin atau Petugas Sarana Prasarana dalam mengelola, memantau, memberikan umpan balik, dan memperbarui progres penyelesaian setiap pengaduan.

Desain aplikasi mengadopsi gaya **Professional SaaS Dashboard** — modern, bersih, dan siap digunakan di lingkungan sekolah nyata.

---

## Fitur Utama

### Role Siswa

| Fitur | Deskripsi |
|---|---|
| Dashboard Siswa | Ringkasan statistik aspirasi pribadi + timeline progres aktif |
| Buat Aspirasi | Form lengkap dengan upload foto, kategori, prioritas, lokasi |
| Riwayat Aspirasi | Tabel + kartu responsif dengan filter & pencarian |
| Detail Aspirasi | Deskripsi lengkap, foto bukti, umpan balik admin, histori perubahan |
| Profil | Update nama/email + ganti kata sandi |
| Notifikasi | Bell dropdown real-time + halaman lengkap |

### Role Admin

| Fitur | Deskripsi |
|---|---|
| Dashboard Admin | Stat cards, 3 chart (bar bulanan, donut status, bar kategori), aktivitas terbaru, aspirasi butuh perhatian |
| Manajemen Aspirasi | Tabel dengan 7 filter, sorting, pagination |
| Detail Pengelolaan | Update status, kirim umpan balik, tambah progres perbaikan dengan slider persentase |
| Laporan & Analytics | KPI cards, chart distribusi, export/print |
| Manajemen Kategori | CRUD kategori dengan inline edit Alpine.js |
| Data Siswa | List siswa terdaftar dengan statistik aspirasi |
| Notifikasi | Semua notifikasi masuk dari sistem |

---

## Stack Teknologi

| Layer | Teknologi |
|---|---|
| **Backend** | Laravel 13 (PHP 8.3) |
| **Frontend** | Laravel Blade + Alpine.js 3 |
| **Styling** | Tailwind CSS (CDN, no build step) |
| **Charts** | Chart.js 4 |
| **Database** | MySQL / MariaDB |
| **DB Management** | HeidiSQL |
| **Font** | Plus Jakarta Sans (Google Fonts) |

### Paket Composer

```
laravel/framework:    ^13.0
laravel/tinker:       ^3.0
laravel/boost:        ^2.7   (dev)
laravel/pint:         ^1.0   (dev)
```

---

## Struktur Proyek

```
sipra/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── CategoryController.php
│   │   │   │   ├── ComplaintController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── ReportController.php
│   │   │   │   └── SiswaController.php
│   │   │   ├── Siswa/
│   │   │   │   ├── ComplaintController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   └── ProfileController.php
│   │   │   ├── AuthController.php
│   │   │   └── NotificationController.php
│   │   └── Middleware/
│   │       └── RoleMiddleware.php
│   ├── Models/
│   │   ├── Category.php
│   │   ├── Complaint.php
│   │   ├── ComplaintHistory.php
│   │   ├── ComplaintImage.php
│   │   ├── ComplaintProgress.php
│   │   ├── Feedback.php
│   │   ├── Notification.php
│   │   └── User.php
│   └── Services/
│       ├── ComplaintService.php
│       └── NotificationService.php
├── database/
│   ├── migrations/          # 8 migration files
│   ├── seeders/             # DatabaseSeeder + 3 seeders
│   └── sipra_db.sql         # Import langsung via HeidiSQL
├── resources/views/
│   ├── admin/
│   │   ├── complaints/      # index.blade.php, show.blade.php
│   │   ├── dashboard.blade.php
│   │   ├── kategori.blade.php
│   │   └── laporan.blade.php
│   ├── auth/
│   │   └── login.blade.php
│   ├── components/          # 10 reusable components
│   ├── layouts/             # app, sidebar, navbar, footer, icons
│   ├── notifications/
│   │   └── index.blade.php
│   └── siswa/
│       ├── dashboard.blade.php
│       ├── detail_aspirasi.blade.php
│       ├── form_aspirasi.blade.php
│       ├── histori.blade.php
│       └── profil.blade.php
└── routes/
    └── web.php              # 36 routes
```

---

## Panduan Setup

### Prasyarat

- PHP 8.2+
- Composer 2.x
- MySQL 8.0+ atau MariaDB 10.4+
- HeidiSQL (untuk manajemen database)

### Langkah 1 — Clone / Buka Proyek

```bash
# Jika belum ada, buka folder:
cd d:\coding\Website\ukk-p3\sipra
```

### Langkah 2 — Install Dependensi PHP

```bash
composer install
```

### Langkah 3 — Konfigurasi Environment

```bash
# Salin file env (jika belum ada)
copy .env.example .env

# Generate app key
php artisan key:generate
```

Edit file `.env` sesuaikan koneksi database:

```dotenv
APP_NAME=SIPRA
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sipra_db
DB_USERNAME=root
DB_PASSWORD=         # sesuaikan password MySQL Anda

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

### Langkah 4 — Setup Database

**Opsi A — Import SQL via HeidiSQL (Direkomendasikan)**

1. Buka HeidiSQL → connect ke server MySQL lokal
2. **File → Load SQL file** → pilih `database/sipra_db.sql`
3. Klik **Execute** (F9) → database `sipra_db` + tabel + data awal terbuat
4. Kemudian lanjut ke Langkah 5 untuk run seeder

**Opsi B — Via Artisan**

```bash
# Buat database terlebih dahulu di HeidiSQL atau MySQL CLI:
# CREATE DATABASE sipra_db CHARACTER SET utf8mb4;

php artisan migrate
```

### Langkah 5 — Isi Data Sample

```bash
php artisan db:seed
```

Seeder akan membuat:
- **2 akun Admin** + **10 akun Siswa**
- **10 kategori** aspirasi lengkap
- **10 aspirasi sample** dengan berbagai status, feedback, progress, dan notifikasi

### Langkah 6 — Storage Symlink

```bash
php artisan storage:link
```

### Langkah 7 — Jalankan Server

```bash
php artisan serve
```

Buka browser: **http://localhost:8000**

---

## Akun Demo

Setelah seeder dijalankan, gunakan akun berikut:

### Admin

| Field | Value |
|---|---|
| **Email** | `admin@sipra.sch.id` |
| **Password** | `admin123` |
| **Role** | Admin |

### Siswa

| Field | Value |
|---|---|
| **NISN** | `0051234567` |
| **Email** | `andi@siswa.sipra.sch.id` |
| **Password** | `siswa123` |
| **Kelas** | XII RPL 1 |

> Semua 10 akun siswa menggunakan password `siswa123`. NISN berurutan dari `0051234567` s.d. `0051234576`.

---

## Alur Penggunaan

### Alur Siswa

```
Login (role: Siswa)
  ↓
Dashboard — lihat statistik & aspirasi aktif
  ↓
Buat Aspirasi — isi form, upload foto
  ↓
Sistem membuat nomor SPR-YYYY-NNNN
Admin menerima notifikasi otomatis
  ↓
Pantau status di Riwayat / Detail Aspirasi
  ↓
Terima notifikasi saat status berubah / feedback masuk
```

### Alur Admin

```
Login (role: Admin)
  ↓
Dashboard — lihat overview & aspirasi butuh perhatian
  ↓
Manajemen Aspirasi — filter, cari, sort
  ↓
Buka Detail Aspirasi — review informasi & foto
  ↓
Panel kanan: [Update Status] [Umpan Balik] [Tambah Progres]
  ↓
Sistem otomatis kirim notifikasi ke siswa
  ↓
Laporan — analitik & export/print
```

### Status Flow

```
Menunggu → Ditinjau → Diproses → Dalam Perbaikan → Selesai
                                                  ↘ Ditolak
```

---

## Skema Database

### Tabel Utama

```
users               — akun siswa & admin (role: siswa | admin)
categories          — kategori aspirasi (10 default)
complaints          — data aspirasi utama
complaint_images    — foto bukti aspirasi (relasi ke complaints)
feedback            — umpan balik admin (relasi ke complaints)
complaint_progress  — log progres perbaikan (relasi ke complaints)
complaint_history   — audit trail setiap perubahan
notifications       — sistem notifikasi in-app
```

### Relasi Utama

```
User         ──< Complaint         (1 user banyak aspirasi)
Category     ──< Complaint         (1 kategori banyak aspirasi)
Complaint    ──< ComplaintImage    (1 aspirasi banyak foto)
Complaint    ──< Feedback          (1 aspirasi banyak umpan balik)
Complaint    ──< ComplaintProgress (1 aspirasi banyak log progres)
Complaint    ──< ComplaintHistory  (1 aspirasi banyak histori)
User         ──< Notification      (1 user banyak notifikasi)
```

---

## Desain Sistem

### Komponen Reusable (Blade Components)

| Component | Keterangan |
|---|---|
| `<x-stat-card>` | Kartu statistik dengan icon + warna dinamis |
| `<x-status-badge>` | Badge status dengan titik warna (6 status) |
| `<x-priority-badge>` | Badge prioritas (4 level) |
| `<x-progress-bar>` | Progress bar dengan warna otomatis per persentase |
| `<x-timeline>` | Timeline vertikal untuk progress steps |
| `<x-card>` | Card wrapper dengan slot title/action |
| `<x-empty-state>` | Tampilan kosong dengan ikon + CTA opsional |
| `<x-page-header>` | Header halaman dengan back button + slot aksi |
| `<x-table-filter>` | Form filter tabel dengan tombol Apply/Reset |
| `<x-form-input>` | Wrapper input dengan error state otomatis |
| `<x-modal>` | Modal dialog Alpine.js |

### Konvensi Warna Status

| Status | Warna |
|---|---|
| Menunggu | Amber |
| Ditinjau | Biru |
| Diproses | Ungu |
| Dalam Perbaikan | Oranye |
| Selesai | Hijau Emerald |
| Ditolak | Merah |

### Nomor Aspirasi

Format otomatis: `SPR-{TAHUN}-{URUTAN}`
Contoh: `SPR-2026-0001`

---

## Perintah Berguna

```bash
# Jalankan server development
php artisan serve

# Reset & isi ulang database
php artisan migrate:fresh --seed

# Bersihkan semua cache
php artisan optimize:clear

# Format kode PHP (Pint)
vendor/bin/pint app/ database/ routes/

# Lihat semua routes
php artisan route:list

# Buka REPL (Tinker)
php artisan tinker
```

---

## Lisensi

Proyek ini dibuat untuk keperluan **Uji Kompetensi Keahlian (UKK)** bidang Rekayasa Perangkat Lunak.

---

<p align="center">
  Dibuat dengan menggunakan Laravel + Tailwind CSS
</p>
