-- ============================================================
-- SIPRA - Sistem Informasi Pengaduan Sarana dan Prasarana
-- Database Schema + Sample Data
-- Compatible: MySQL 8.0+ / MariaDB 10.4+
-- Import via: HeidiSQL > File > Run SQL File
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+07:00";
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- ------------------------------------------------------------
-- Create Database
-- ------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `sipra_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `sipra_db`;

-- ============================================================
-- TABLE: users
-- ============================================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`             VARCHAR(255) NOT NULL,
  `email`            VARCHAR(255) NOT NULL,
  `nisn`             VARCHAR(20)  DEFAULT NULL,
  `class`            VARCHAR(20)  DEFAULT NULL,
  `password`         VARCHAR(255) NOT NULL,
  `role`             ENUM('siswa','admin') NOT NULL DEFAULT 'siswa',
  `avatar`           VARCHAR(255) DEFAULT NULL,
  `remember_token`   VARCHAR(100) DEFAULT NULL,
  `created_at`       TIMESTAMP NULL DEFAULT NULL,
  `updated_at`       TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_nisn_unique` (`nisn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: password_reset_tokens
-- ============================================================
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email`      VARCHAR(255) NOT NULL,
  `token`      VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: categories
-- ============================================================
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(255) NOT NULL,
  `slug`        VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `icon`        VARCHAR(100) NOT NULL DEFAULT 'heroicon-building-office',
  `color`       VARCHAR(50)  NOT NULL DEFAULT 'indigo',
  `is_active`   TINYINT(1)   NOT NULL DEFAULT 1,
  `sort_order`  INT          NOT NULL DEFAULT 0,
  `created_at`  TIMESTAMP NULL DEFAULT NULL,
  `updated_at`  TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: complaints
-- ============================================================
DROP TABLE IF EXISTS `complaints`;
CREATE TABLE `complaints` (
  `id`                   BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `complaint_number`     VARCHAR(30) NOT NULL COMMENT 'Nomor unik pengaduan, cth: SPR-2024-0001',
  `user_id`              BIGINT UNSIGNED NOT NULL,
  `category_id`          BIGINT UNSIGNED NOT NULL,
  `title`                VARCHAR(255) NOT NULL,
  `location`             VARCHAR(255) NOT NULL,
  `description`          TEXT NOT NULL,
  `priority`             ENUM('rendah','sedang','tinggi','kritis') NOT NULL DEFAULT 'sedang',
  `status`               ENUM('menunggu','ditinjau','diproses','dalam_perbaikan','selesai','ditolak') NOT NULL DEFAULT 'menunggu',
  `progress_percentage`  INT NOT NULL DEFAULT 0,
  `reviewed_at`          TIMESTAMP NULL DEFAULT NULL,
  `resolved_at`          TIMESTAMP NULL DEFAULT NULL,
  `assigned_to`          BIGINT UNSIGNED DEFAULT NULL,
  `created_at`           TIMESTAMP NULL DEFAULT NULL,
  `updated_at`           TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `complaints_complaint_number_unique` (`complaint_number`),
  KEY `complaints_user_status` (`user_id`, `status`),
  KEY `complaints_category_status` (`category_id`, `status`),
  KEY `complaints_status_priority` (`status`, `priority`),
  KEY `complaints_created_at` (`created_at`),
  CONSTRAINT `complaints_user_id_foreign`    FOREIGN KEY (`user_id`)    REFERENCES `users`      (`id`) ON DELETE CASCADE,
  CONSTRAINT `complaints_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `complaints_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users`      (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: complaint_images
-- ============================================================
DROP TABLE IF EXISTS `complaint_images`;
CREATE TABLE `complaint_images` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `complaint_id`  BIGINT UNSIGNED NOT NULL,
  `image_path`    VARCHAR(255) NOT NULL,
  `original_name` VARCHAR(255) DEFAULT NULL,
  `mime_type`     VARCHAR(50)  DEFAULT NULL,
  `file_size`     BIGINT UNSIGNED DEFAULT NULL COMMENT 'Bytes',
  `sort_order`    INT NOT NULL DEFAULT 0,
  `created_at`    TIMESTAMP NULL DEFAULT NULL,
  `updated_at`    TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `complaint_images_complaint_id` (`complaint_id`),
  CONSTRAINT `complaint_images_complaint_id_foreign` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: feedback
-- ============================================================
DROP TABLE IF EXISTS `feedback`;
CREATE TABLE `feedback` (
  `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `complaint_id` BIGINT UNSIGNED NOT NULL,
  `admin_id`     BIGINT UNSIGNED NOT NULL,
  `message`      TEXT NOT NULL,
  `type`         ENUM('informasi','tindakan','penolakan','selesai') NOT NULL DEFAULT 'informasi',
  `created_at`   TIMESTAMP NULL DEFAULT NULL,
  `updated_at`   TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `feedback_complaint_created` (`complaint_id`, `created_at`),
  CONSTRAINT `feedback_complaint_id_foreign` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE CASCADE,
  CONSTRAINT `feedback_admin_id_foreign`     FOREIGN KEY (`admin_id`)     REFERENCES `users`      (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: complaint_progress
-- ============================================================
DROP TABLE IF EXISTS `complaint_progress`;
CREATE TABLE `complaint_progress` (
  `id`                   BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `complaint_id`         BIGINT UNSIGNED NOT NULL,
  `created_by`           BIGINT UNSIGNED NOT NULL,
  `title`                VARCHAR(255) NOT NULL,
  `description`          TEXT NOT NULL,
  `progress_percentage`  INT NOT NULL DEFAULT 0 COMMENT '0-100',
  `image_path`           VARCHAR(255) DEFAULT NULL COMMENT 'Foto dokumentasi progres',
  `work_date`            DATE DEFAULT NULL COMMENT 'Tanggal pengerjaan',
  `created_at`           TIMESTAMP NULL DEFAULT NULL,
  `updated_at`           TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `complaint_progress_complaint_created` (`complaint_id`, `created_at`),
  CONSTRAINT `complaint_progress_complaint_id_foreign` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE CASCADE,
  CONSTRAINT `complaint_progress_created_by_foreign`  FOREIGN KEY (`created_by`)   REFERENCES `users`      (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: complaint_history
-- ============================================================
DROP TABLE IF EXISTS `complaint_history`;
CREATE TABLE `complaint_history` (
  `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `complaint_id`    BIGINT UNSIGNED NOT NULL,
  `previous_status` VARCHAR(30)  DEFAULT NULL,
  `new_status`      VARCHAR(30)  DEFAULT NULL,
  `action`          VARCHAR(100) DEFAULT NULL COMMENT 'update_status, add_feedback, add_progress, etc.',
  `note`            TEXT DEFAULT NULL,
  `changed_by`      BIGINT UNSIGNED NOT NULL,
  `created_at`      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `complaint_history_complaint_created` (`complaint_id`, `created_at`),
  CONSTRAINT `complaint_history_complaint_id_foreign` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE CASCADE,
  CONSTRAINT `complaint_history_changed_by_foreign`   FOREIGN KEY (`changed_by`)   REFERENCES `users`      (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: notifications
-- ============================================================
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`      BIGINT UNSIGNED NOT NULL,
  `title`        VARCHAR(255) NOT NULL,
  `message`      TEXT NOT NULL,
  `type`         VARCHAR(50) NOT NULL DEFAULT 'info' COMMENT 'info, success, warning, danger',
  `icon`         VARCHAR(100) DEFAULT NULL,
  `complaint_id` BIGINT UNSIGNED DEFAULT NULL,
  `action_url`   VARCHAR(500) DEFAULT NULL,
  `is_read`      TINYINT(1)  NOT NULL DEFAULT 0,
  `read_at`      TIMESTAMP NULL DEFAULT NULL,
  `created_at`   TIMESTAMP NULL DEFAULT NULL,
  `updated_at`   TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_user_read`    (`user_id`, `is_read`),
  KEY `notifications_user_created` (`user_id`, `created_at`),
  CONSTRAINT `notifications_user_id_foreign`      FOREIGN KEY (`user_id`)      REFERENCES `users`      (`id`) ON DELETE CASCADE,
  CONSTRAINT `notifications_complaint_id_foreign` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SAMPLE DATA
-- ============================================================

-- Users (passwords: admin=admin123, siswa=siswa123 — generated via Hash::make)
-- Run `php artisan db:seed` instead for correct bcrypt hashes.
-- These rows are reference only; actual seeding uses UserSeeder.php
INSERT INTO `users` (`id`, `name`, `email`, `nisn`, `class`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1,  'Admin SIPRA',     'admin@sipra.sch.id',              NULL,         NULL,          'run_db_seed', 'admin', NOW(), NOW()),
(2,  'Budi Santoso',    'budi.admin@sipra.sch.id',         NULL,         NULL,          'run_db_seed', 'admin', NOW(), NOW()),
(3,  'Andi Pratama',    'andi@siswa.sipra.sch.id',         '0051234567', 'XII RPL 1',   'run_db_seed', 'siswa', NOW(), NOW()),
(4,  'Siti Rahayu',     'siti@siswa.sipra.sch.id',         '0051234568', 'XII RPL 2',   'run_db_seed', 'siswa', NOW(), NOW()),
(5,  'Bima Ardiansyah', 'bima@siswa.sipra.sch.id',         '0051234569', 'XI TKJ 1',    'run_db_seed', 'siswa', NOW(), NOW()),
(6,  'Dina Safitri',    'dina@siswa.sipra.sch.id',         '0051234570', 'XI TKJ 2',    'run_db_seed', 'siswa', NOW(), NOW()),
(7,  'Rizky Firmansyah','rizky@siswa.sipra.sch.id',        '0051234571', 'X DKV 1',     'run_db_seed', 'siswa', NOW(), NOW()),
(8,  'Nurul Hidayah',   'nurul@siswa.sipra.sch.id',        '0051234572', 'X TSM 1',     'run_db_seed', 'siswa', NOW(), NOW()),
(9,  'Fajar Setiawan',  'fajar@siswa.sipra.sch.id',        '0051234573', 'XII TKR 1',   'run_db_seed', 'siswa', NOW(), NOW()),
(10, 'Maya Kurniawati', 'maya@siswa.sipra.sch.id',         '0051234574', 'XI ATPH 1',   'run_db_seed', 'siswa', NOW(), NOW()),
(11, 'Hendra Gunawan',  'hendra@siswa.sipra.sch.id',       '0051234575', 'X APT 1',     'run_db_seed', 'siswa', NOW(), NOW()),
(12, 'Putri Anggraini', 'putri@siswa.sipra.sch.id',        '0051234576', 'XII TKJ 1',   'run_db_seed', 'siswa', NOW(), NOW());

-- Categories
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `color`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1,  'Ruang Kelas',          'ruang-kelas',         'Kerusakan atau kekurangan fasilitas di ruang kelas.',                        'academic-cap',               'blue',   1, 1,  NOW(), NOW()),
(2,  'Toilet',               'toilet',              'Masalah kebersihan, kerusakan, atau kekurangan fasilitas toilet.',            'home-modern',                'teal',   1, 2,  NOW(), NOW()),
(3,  'Laboratorium',         'laboratorium',        'Peralatan laboratorium yang rusak atau tidak berfungsi.',                     'beaker',                     'purple', 1, 3,  NOW(), NOW()),
(4,  'Perpustakaan',         'perpustakaan',        'Koleksi buku, fasilitas baca, atau sistem perpustakaan.',                    'book-open',                  'indigo', 1, 4,  NOW(), NOW()),
(5,  'Lapangan',             'lapangan',            'Kondisi lapangan olahraga atau area luar sekolah.',                          'map',                        'green',  1, 5,  NOW(), NOW()),
(6,  'Peralatan Elektronik', 'peralatan-elektronik','Proyektor, AC, kipas angin, komputer, atau peralatan elektronik bermasalah.', 'computer-desktop',           'cyan',   1, 6,  NOW(), NOW()),
(7,  'Jaringan / Internet',  'jaringan-internet',   'Masalah koneksi WiFi, jaringan LAN, atau akses internet.',                   'wifi',                       'blue',   1, 7,  NOW(), NOW()),
(8,  'Kebersihan',           'kebersihan',          'Kondisi kebersihan lingkungan sekolah.',                                     'sparkles',                   'yellow', 1, 8,  NOW(), NOW()),
(9,  'Keamanan',             'keamanan',            'Masalah keamanan lingkungan sekolah, pagar, kunci, atau pencahayaan.',        'shield-check',               'red',    1, 9,  NOW(), NOW()),
(10, 'Lainnya',              'lainnya',             'Aspirasi atau pengaduan lain yang tidak termasuk kategori tersedia.',         'ellipsis-horizontal-circle', 'orange', 1, 10, NOW(), NOW());

COMMIT;

-- ============================================================
-- NOTES FOR IMPORT
-- ============================================================
-- CARA YANG DIREKOMENDASIKAN (password bcrypt benar):
--   1. Import file ini via HeidiSQL untuk membuat struktur tabel
--      File > Load SQL File > Execute
--   2. Jalankan seeder untuk data + password yang benar:
--      php artisan db:seed
--
-- Akun default setelah db:seed:
--   Admin : admin@sipra.sch.id        / admin123
--   Siswa : andi@siswa.sipra.sch.id   / siswa123  (atau NISN: 0051234567)
--
-- Reset bersih:
--   php artisan migrate:fresh --seed
-- ============================================================
