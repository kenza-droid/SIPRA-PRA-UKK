<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ──────────────────────────────────────────
        User::create([
            'name' => 'Admin SIPRA',
            'email' => 'admin@sipra.sch.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'nisn' => null,
            'class' => null,
        ]);

        User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi.admin@sipra.sch.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'nisn' => null,
            'class' => null,
        ]);

        // ── Siswa ──────────────────────────────────────────
        $siswaData = [
            ['name' => 'Andi Pratama',        'email' => 'andi@siswa.sipra.sch.id',    'nisn' => '0051234567', 'class' => 'XII RPL 1'],
            ['name' => 'Siti Rahayu',         'email' => 'siti@siswa.sipra.sch.id',    'nisn' => '0051234568', 'class' => 'XII RPL 2'],
            ['name' => 'Bima Ardiansyah',     'email' => 'bima@siswa.sipra.sch.id',    'nisn' => '0051234569', 'class' => 'XI TKJ 1'],
            ['name' => 'Dina Safitri',        'email' => 'dina@siswa.sipra.sch.id',    'nisn' => '0051234570', 'class' => 'XI TKJ 2'],
            ['name' => 'Rizky Firmansyah',    'email' => 'rizky@siswa.sipra.sch.id',   'nisn' => '0051234571', 'class' => 'X DKV 1'],
            ['name' => 'Nurul Hidayah',       'email' => 'nurul@siswa.sipra.sch.id',   'nisn' => '0051234572', 'class' => 'X TSM 1'],
            ['name' => 'Fajar Setiawan',      'email' => 'fajar@siswa.sipra.sch.id',   'nisn' => '0051234573', 'class' => 'XII TKR 1'],
            ['name' => 'Maya Kurniawati',     'email' => 'maya@siswa.sipra.sch.id',    'nisn' => '0051234574', 'class' => 'XI ATPH 1'],
            ['name' => 'Hendra Gunawan',      'email' => 'hendra@siswa.sipra.sch.id',  'nisn' => '0051234575', 'class' => 'X APT 1'],
            ['name' => 'Putri Anggraini',     'email' => 'putri@siswa.sipra.sch.id',   'nisn' => '0051234576', 'class' => 'XII TKJ 1'],
        ];

        foreach ($siswaData as $siswa) {
            User::create(array_merge($siswa, [
                'password' => Hash::make('siswa123'),
                'role' => 'siswa',
            ]));
        }
    }
}
