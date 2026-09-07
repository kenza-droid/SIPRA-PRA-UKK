<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Ruang Kelas',
                'description' => 'Kerusakan atau kekurangan fasilitas di ruang kelas, termasuk meja, kursi, papan tulis, dan pencahayaan.',
                'icon' => 'academic-cap',
                'color' => 'blue',
                'sort_order' => 1,
            ],
            [
                'name' => 'Toilet',
                'description' => 'Masalah kebersihan, kerusakan, atau kekurangan fasilitas toilet dan kamar mandi.',
                'icon' => 'home-modern',
                'color' => 'teal',
                'sort_order' => 2,
            ],
            [
                'name' => 'Laboratorium',
                'description' => 'Peralatan laboratorium komputer, fisika, kimia, atau biologi yang rusak atau tidak berfungsi.',
                'icon' => 'beaker',
                'color' => 'purple',
                'sort_order' => 3,
            ],
            [
                'name' => 'Perpustakaan',
                'description' => 'Koleksi buku, fasilitas baca, atau sistem perpustakaan yang perlu diperbaiki.',
                'icon' => 'book-open',
                'color' => 'indigo',
                'sort_order' => 4,
            ],
            [
                'name' => 'Lapangan',
                'description' => 'Kondisi lapangan olahraga, taman, atau area luar sekolah yang memerlukan perhatian.',
                'icon' => 'map',
                'color' => 'green',
                'sort_order' => 5,
            ],
            [
                'name' => 'Peralatan Elektronik',
                'description' => 'Proyektor, AC, kipas angin, komputer, atau peralatan elektronik lainnya yang bermasalah.',
                'icon' => 'computer-desktop',
                'color' => 'cyan',
                'sort_order' => 6,
            ],
            [
                'name' => 'Jaringan / Internet',
                'description' => 'Masalah koneksi WiFi, jaringan LAN, atau akses internet di lingkungan sekolah.',
                'icon' => 'wifi',
                'color' => 'blue',
                'sort_order' => 7,
            ],
            [
                'name' => 'Kebersihan',
                'description' => 'Kondisi kebersihan lingkungan sekolah yang perlu mendapat perhatian lebih.',
                'icon' => 'sparkles',
                'color' => 'yellow',
                'sort_order' => 8,
            ],
            [
                'name' => 'Keamanan',
                'description' => 'Masalah keamanan lingkungan sekolah, pagar, kunci, atau pencahayaan area tertentu.',
                'icon' => 'shield-check',
                'color' => 'red',
                'sort_order' => 9,
            ],
            [
                'name' => 'Lainnya',
                'description' => 'Aspirasi atau pengaduan lain yang tidak termasuk dalam kategori yang tersedia.',
                'icon' => 'ellipsis-horizontal-circle',
                'color' => 'orange',
                'sort_order' => 10,
            ],
        ];

        foreach ($categories as $cat) {
            Category::create(array_merge($cat, [
                'slug' => Str::slug($cat['name']),
                'is_active' => true,
            ]));
        }
    }
}
