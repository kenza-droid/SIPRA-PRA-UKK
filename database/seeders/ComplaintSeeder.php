<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\ComplaintHistory;
use App\Models\ComplaintProgress;
use App\Models\Feedback;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ComplaintSeeder extends Seeder
{
    public function run(): void
    {
        $siswaList = User::where('role', 'siswa')->get();
        $adminList = User::where('role', 'admin')->get();
        $admin = $adminList->first();
        $categories = Category::all();

        $sampleComplaints = [
            // ── Selesai ──────────────────────────────────────
            [
                'title' => 'Proyektor Ruang XII RPL 1 Tidak Berfungsi',
                'category' => 'Peralatan Elektronik',
                'location' => 'Ruang XII RPL 1, Gedung B Lantai 2',
                'description' => 'Proyektor di ruang XII RPL 1 sudah rusak selama 2 minggu. Layar tidak menampilkan gambar meskipun sudah dinyalakan. Kondisi ini mengganggu proses belajar mengajar karena guru tidak dapat menampilkan materi pembelajaran secara visual.',
                'priority' => 'tinggi',
                'status' => 'selesai',
                'user_index' => 0,
                'days_ago' => 45,
                'progress' => 100,
                'feedback_msg' => 'Proyektor telah berhasil diperbaiki oleh teknisi pada tanggal 5 Agustus. Silakan konfirmasi jika masih ada kendala.',
            ],
            [
                'title' => 'Kran Air Toilet Putra Lantai 1 Bocor',
                'category' => 'Toilet',
                'location' => 'Toilet Putra Lantai 1, Gedung A',
                'description' => 'Kran air di toilet putra lantai 1 mengalami kebocoran parah. Air terus mengalir meskipun sudah dimatikan. Kondisi ini menyebabkan lantai selalu basah dan berpotensi membahayakan siswa.',
                'priority' => 'tinggi',
                'status' => 'selesai',
                'user_index' => 2,
                'days_ago' => 30,
                'progress' => 100,
                'feedback_msg' => 'Kran telah diganti dengan yang baru. Terima kasih atas laporan Anda.',
            ],
            // ── Dalam Perbaikan ──────────────────────────────
            [
                'title' => 'Kursi Laboratorium Komputer Banyak yang Rusak',
                'category' => 'Laboratorium',
                'location' => 'Lab Komputer 1, Gedung C Lantai 1',
                'description' => 'Sebanyak 8 kursi di laboratorium komputer 1 sudah dalam kondisi rusak, beberapa kaki kursi patah dan sandaran tidak berfungsi. Kondisi ini membuat siswa tidak nyaman saat praktikum berlangsung.',
                'priority' => 'sedang',
                'status' => 'dalam_perbaikan',
                'user_index' => 1,
                'days_ago' => 20,
                'progress' => 60,
                'feedback_msg' => 'Sudah dipesan kursi pengganti sebanyak 8 unit. Estimasi tiba dalam 1 minggu.',
            ],
            [
                'title' => 'Jaringan WiFi di Perpustakaan Sangat Lambat',
                'category' => 'Jaringan / Internet',
                'location' => 'Perpustakaan, Gedung A Lantai 2',
                'description' => 'Koneksi WiFi di area perpustakaan sangat lambat bahkan sering terputus. Padahal banyak siswa yang membutuhkan akses internet untuk mencari referensi belajar dan mengerjakan tugas. Kondisi ini sudah berlangsung lebih dari 3 minggu.',
                'priority' => 'sedang',
                'status' => 'dalam_perbaikan',
                'user_index' => 3,
                'days_ago' => 15,
                'progress' => 40,
                'feedback_msg' => 'Tim IT sedang melakukan upgrade perangkat access point. Proses selesai dalam 3-5 hari kerja.',
            ],
            // ── Diproses ─────────────────────────────────────
            [
                'title' => 'Lampu Ruang Kelas X DKV 1 Mati Semua',
                'category' => 'Ruang Kelas',
                'location' => 'Ruang X DKV 1, Gedung D Lantai 1',
                'description' => 'Seluruh lampu di ruang kelas X DKV 1 tidak menyala sejak Senin kemarin. Ruangan menjadi gelap terutama pada pagi hari dan hari mendung. Kegiatan belajar sangat terganggu.',
                'priority' => 'tinggi',
                'status' => 'diproses',
                'user_index' => 4,
                'days_ago' => 10,
                'progress' => 25,
                'feedback_msg' => 'Laporan telah diterima dan ditugaskan ke teknisi listrik sekolah untuk segera ditangani.',
            ],
            [
                'title' => 'Papan Tulis Ruang XI TKJ 2 Perlu Diganti',
                'category' => 'Ruang Kelas',
                'location' => 'Ruang XI TKJ 2, Gedung B Lantai 1',
                'description' => 'Papan tulis di ruang XI TKJ 2 sudah sangat usang, permukaan sudah tidak rata sehingga tulisan sulit dibaca. Penggunaan spidol pun sering tidak terlihat jelas dari bangku belakang.',
                'priority' => 'sedang',
                'status' => 'diproses',
                'user_index' => 5,
                'days_ago' => 8,
                'progress' => 20,
                'feedback_msg' => 'Sudah diajukan pengadaan papan tulis baru ke bagian sarana. Menunggu persetujuan anggaran.',
            ],
            // ── Ditinjau ─────────────────────────────────────
            [
                'title' => 'Kebocoran Atap Ruang Perpustakaan saat Hujan',
                'category' => 'Perpustakaan',
                'location' => 'Perpustakaan, Gedung A Lantai 2',
                'description' => 'Atap perpustakaan mengalami kebocoran pada bagian sudut kanan depan. Setiap hujan turun, air merembes masuk dan membasahi rak buku terdekat. Ini berpotensi merusak koleksi buku yang ada.',
                'priority' => 'kritis',
                'status' => 'ditinjau',
                'user_index' => 6,
                'days_ago' => 5,
                'progress' => 10,
                'feedback_msg' => null,
            ],
            [
                'title' => 'Lapangan Basket Retak dan Berbahaya',
                'category' => 'Lapangan',
                'location' => 'Lapangan Basket, Area Olahraga',
                'description' => 'Permukaan lapangan basket mengalami keretakan yang cukup parah di beberapa titik. Hal ini berbahaya bagi siswa yang bermain karena bisa menyebabkan terpeleset atau tersandung.',
                'priority' => 'tinggi',
                'status' => 'ditinjau',
                'user_index' => 7,
                'days_ago' => 4,
                'progress' => 0,
                'feedback_msg' => null,
            ],
            // ── Menunggu ─────────────────────────────────────
            [
                'title' => 'AC Ruang Server Tidak Berfungsi Optimal',
                'category' => 'Peralatan Elektronik',
                'location' => 'Ruang Server, Gedung TIK',
                'description' => 'AC di ruang server tidak dapat mendinginkan ruangan dengan baik. Suhu ruangan menjadi terlalu panas yang dapat merusak perangkat server sekolah. Kondisi ini sudah berlangsung 1 minggu.',
                'priority' => 'kritis',
                'status' => 'menunggu',
                'user_index' => 8,
                'days_ago' => 2,
                'progress' => 0,
                'feedback_msg' => null,
            ],
            [
                'title' => 'Kunci Lemari Buku Perpustakaan Rusak',
                'category' => 'Keamanan',
                'location' => 'Perpustakaan, Gedung A Lantai 2',
                'description' => 'Beberapa lemari penyimpanan buku referensi di perpustakaan memiliki kunci yang rusak atau hilang. Hal ini menyebabkan buku-buku berharga tidak dapat diamankan dengan baik.',
                'priority' => 'rendah',
                'status' => 'menunggu',
                'user_index' => 9,
                'days_ago' => 1,
                'progress' => 0,
                'feedback_msg' => null,
            ],
        ];

        foreach ($sampleComplaints as $index => $data) {
            $user = $siswaList->get($data['user_index']);
            $category = $categories->firstWhere('name', $data['category']);

            $createdAt = Carbon::now()->subDays($data['days_ago']);
            $number = sprintf('SPR-%s-%04d', $createdAt->year, $index + 1);

            $resolvedAt = null;
            if ($data['status'] === 'selesai') {
                $resolvedAt = $createdAt->copy()->addDays(rand(5, 15));
            }

            $complaint = Complaint::create([
                'complaint_number' => $number,
                'user_id' => $user->id,
                'category_id' => $category->id,
                'title' => $data['title'],
                'location' => $data['location'],
                'description' => $data['description'],
                'priority' => $data['priority'],
                'status' => $data['status'],
                'progress_percentage' => $data['progress'],
                'reviewed_at' => in_array($data['status'], ['ditinjau', 'diproses', 'dalam_perbaikan', 'selesai'])
                                            ? $createdAt->copy()->addHours(rand(2, 24)) : null,
                'resolved_at' => $resolvedAt,
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addDays(rand(0, $data['days_ago'])),
            ]);

            // ── Histori ──────────────────────────────────────
            ComplaintHistory::create([
                'complaint_id' => $complaint->id,
                'previous_status' => null,
                'new_status' => 'menunggu',
                'action' => 'create',
                'note' => 'Aspirasi dibuat oleh siswa.',
                'changed_by' => $user->id,
                'created_at' => $createdAt,
            ]);

            if ($data['status'] !== 'menunggu') {
                $statusFlow = match ($data['status']) {
                    'ditinjau' => ['menunggu' => 'ditinjau'],
                    'diproses' => ['menunggu' => 'ditinjau', 'ditinjau' => 'diproses'],
                    'dalam_perbaikan' => ['menunggu' => 'ditinjau', 'ditinjau' => 'diproses', 'diproses' => 'dalam_perbaikan'],
                    'selesai' => ['menunggu' => 'ditinjau', 'ditinjau' => 'diproses', 'diproses' => 'dalam_perbaikan', 'dalam_perbaikan' => 'selesai'],
                    default => [],
                };

                $historyTime = $createdAt->copy()->addHours(4);
                foreach ($statusFlow as $from => $to) {
                    ComplaintHistory::create([
                        'complaint_id' => $complaint->id,
                        'previous_status' => $from,
                        'new_status' => $to,
                        'action' => 'update_status',
                        'note' => 'Status diperbarui oleh admin.',
                        'changed_by' => $admin->id,
                        'created_at' => $historyTime,
                    ]);
                    $historyTime->addDays(rand(1, 3));
                }
            }

            // ── Feedback ─────────────────────────────────────
            if ($data['feedback_msg']) {
                Feedback::create([
                    'complaint_id' => $complaint->id,
                    'admin_id' => $admin->id,
                    'message' => $data['feedback_msg'],
                    'type' => $data['status'] === 'selesai' ? 'selesai' : 'tindakan',
                    'created_at' => $createdAt->copy()->addDays(2),
                    'updated_at' => $createdAt->copy()->addDays(2),
                ]);

                ComplaintHistory::create([
                    'complaint_id' => $complaint->id,
                    'previous_status' => $data['status'],
                    'new_status' => $data['status'],
                    'action' => 'add_feedback',
                    'note' => 'Admin memberikan umpan balik.',
                    'changed_by' => $admin->id,
                    'created_at' => $createdAt->copy()->addDays(2),
                ]);

                // Notifikasi ke siswa
                Notification::create([
                    'user_id' => $user->id,
                    'title' => 'Umpan Balik Baru',
                    'message' => "Admin memberikan umpan balik pada aspirasi \"{$data['title']}\".",
                    'type' => 'info',
                    'icon' => 'chat-bubble-left-right',
                    'complaint_id' => $complaint->id,
                    'action_url' => '/siswa/aspirasi/'.$complaint->id,
                    'is_read' => $data['status'] === 'selesai',
                    'created_at' => $createdAt->copy()->addDays(2),
                    'updated_at' => $createdAt->copy()->addDays(2),
                ]);
            }

            // ── Progres (untuk yang sudah cukup jauh) ────────
            if ($data['progress'] > 0 && in_array($data['status'], ['dalam_perbaikan', 'selesai'])) {
                ComplaintProgress::create([
                    'complaint_id' => $complaint->id,
                    'created_by' => $admin->id,
                    'title' => 'Pengecekan Lapangan',
                    'description' => 'Tim sarana telah melakukan pengecekan langsung ke lokasi dan memastikan kerusakan yang dilaporkan.',
                    'progress_percentage' => 20,
                    'work_date' => $createdAt->copy()->addDays(3)->toDateString(),
                    'created_at' => $createdAt->copy()->addDays(3),
                    'updated_at' => $createdAt->copy()->addDays(3),
                ]);

                if ($data['progress'] >= 50) {
                    ComplaintProgress::create([
                        'complaint_id' => $complaint->id,
                        'created_by' => $admin->id,
                        'title' => 'Perbaikan Sedang Berjalan',
                        'description' => 'Perbaikan sedang dilakukan oleh petugas sarana prasarana sekolah.',
                        'progress_percentage' => $data['status'] === 'selesai' ? 75 : $data['progress'],
                        'work_date' => $createdAt->copy()->addDays(7)->toDateString(),
                        'created_at' => $createdAt->copy()->addDays(7),
                        'updated_at' => $createdAt->copy()->addDays(7),
                    ]);
                }

                if ($data['status'] === 'selesai') {
                    ComplaintProgress::create([
                        'complaint_id' => $complaint->id,
                        'created_by' => $admin->id,
                        'title' => 'Perbaikan Selesai',
                        'description' => 'Seluruh perbaikan telah selesai dilakukan dan fasilitas sudah dapat digunakan kembali.',
                        'progress_percentage' => 100,
                        'work_date' => $resolvedAt?->toDateString(),
                        'created_at' => $resolvedAt,
                        'updated_at' => $resolvedAt,
                    ]);
                }

                // Notifikasi progres
                Notification::create([
                    'user_id' => $user->id,
                    'title' => 'Progres Perbaikan Diperbarui',
                    'message' => "Progres perbaikan aspirasi \"{$data['title']}\" telah diperbarui.",
                    'type' => 'success',
                    'icon' => 'chart-bar',
                    'complaint_id' => $complaint->id,
                    'action_url' => '/siswa/aspirasi/'.$complaint->id,
                    'is_read' => $data['status'] === 'selesai',
                    'created_at' => $createdAt->copy()->addDays(7),
                    'updated_at' => $createdAt->copy()->addDays(7),
                ]);
            }

            // ── Notifikasi status update ──────────────────────
            if ($data['status'] !== 'menunggu') {
                Notification::create([
                    'user_id' => $user->id,
                    'title' => 'Status Aspirasi Diperbarui',
                    'message' => "Status aspirasi \"{$data['title']}\" telah berubah.",
                    'type' => $data['status'] === 'selesai' ? 'success' : 'info',
                    'icon' => 'arrow-path',
                    'complaint_id' => $complaint->id,
                    'action_url' => '/siswa/aspirasi/'.$complaint->id,
                    'is_read' => in_array($data['status'], ['selesai', 'diproses']),
                    'created_at' => $createdAt->copy()->addHours(6),
                    'updated_at' => $createdAt->copy()->addHours(6),
                ]);
            }

            // ── Notifikasi ke admin untuk aspirasi baru ───────
            foreach ($adminList as $adm) {
                Notification::create([
                    'user_id' => $adm->id,
                    'title' => 'Aspirasi Baru Masuk',
                    'message' => "Aspirasi baru dari {$user->name}: \"{$data['title']}\".",
                    'type' => in_array($data['priority'], ['kritis']) ? 'danger' : 'info',
                    'icon' => 'inbox',
                    'complaint_id' => $complaint->id,
                    'action_url' => '/admin/aspirasi/'.$complaint->id,
                    'is_read' => $data['status'] !== 'menunggu',
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }
    }
}
