-- MySQL schema baseline for Absensi PPSU.
-- Target: MySQL 8.0+ / MariaDB 10.6+.
-- Usage:
--   mysql -u root -p -e "CREATE DATABASE absensi_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
--   mysql -u root -p absensi_app < database/schema/mysql-schema.sql
--   cp .env.mysql.example .env
--   php artisan key:generate
--
-- This file includes the migrations table so old migrations are not rerun
-- after importing the baseline schema. Future migrations will still run normally.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `personal_access_tokens`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `failed_jobs`;
DROP TABLE IF EXISTS `user_sensitive`;
DROP TABLE IF EXISTS `activity_log`;
DROP TABLE IF EXISTS `kalender`;
DROP TABLE IF EXISTS `notifikasi`;
DROP TABLE IF EXISTS `sanksi`;
DROP TABLE IF EXISTS `tugas`;
DROP TABLE IF EXISTS `cuti`;
DROP TABLE IF EXISTS `absensi`;
DROP TABLE IF EXISTS `shifts`;
DROP TABLE IF EXISTS `pengaturan`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `tempat_tugas`;
DROP TABLE IF EXISTS `roles`;
DROP TABLE IF EXISTS `periode`;
DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `roles` (
  `id_role` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_role` varchar(50) NOT NULL,
  PRIMARY KEY (`id_role`),
  UNIQUE KEY `roles_nama_role_unique` (`nama_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tempat_tugas` (
  `id_tempat` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_tempat` varchar(150) NOT NULL,
  `alamat` text NULL,
  `latitude` decimal(10,7) NULL,
  `longitude` decimal(10,7) NULL,
  PRIMARY KEY (`id_tempat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id_user` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(150) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(150) NOT NULL,
  `id_role` int NOT NULL,
  `id_tempat` int NULL,
  `regu` varchar(20) NULL,
  `is_ketua_regu` tinyint(1) NOT NULL DEFAULT 0,
  `shift` varchar(30) NULL,
  `status_aktif` varchar(20) NOT NULL DEFAULT 'aktif',
  `no_hp` varchar(30) NULL,
  `alamat` text NULL,
  `jabatan` varchar(100) NULL,
  `foto_profil` varchar(255) NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_idx` (`id_role`),
  KEY `users_tempat_idx` (`id_tempat`),
  KEY `users_created_at_idx` (`created_at`),
  KEY `users_regu_ketua_idx` (`regu`, `is_ketua_regu`),
  KEY `users_status_role_idx` (`status_aktif`, `id_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `periode` (
  `id_periode` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_periode` varchar(100) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'nonaktif',
  PRIMARY KEY (`id_periode`),
  UNIQUE KEY `periode_nama_periode_unique` (`nama_periode`),
  KEY `periode_aktif_tanggal_idx` (`status`, `tanggal_mulai`, `tanggal_selesai`, `id_periode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `absensi` (
  `id_absensi` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint unsigned NOT NULL,
  `id_periode` bigint unsigned NULL,
  `tanggal` date NOT NULL,
  `shift` varchar(30) NULL,
  `jam_masuk` time NULL,
  `foto_masuk` varchar(255) NULL,
  `latitude_masuk` decimal(10,7) NULL,
  `longitude_masuk` decimal(10,7) NULL,
  `lokasi_masuk` text NULL,
  `jam_istirahat_mulai` time NULL,
  `jam_istirahat_selesai` time NULL,
  `jam_pulang` time NULL,
  `foto_pulang` varchar(255) NULL,
  `latitude_pulang` decimal(10,7) NULL,
  `longitude_pulang` decimal(10,7) NULL,
  `lokasi_pulang` text NULL,
  `status` enum('hadir','telat','tidak_hadir','diluar_area','tidak_absen','akses_dibuka','cuti') NOT NULL DEFAULT 'hadir',
  `keterangan` text NULL,
  `approval_masuk_status` varchar(30) NULL,
  `approval_masuk_requested_at` timestamp NULL,
  `approval_masuk_forwarded_by` bigint unsigned NULL,
  `approval_masuk_forwarded_at` timestamp NULL,
  `approval_masuk_approved_by` bigint unsigned NULL,
  `approval_masuk_reason` text NULL,
  `approval_pulang_status` varchar(30) NULL,
  `approval_pulang_requested_at` timestamp NULL,
  `approval_pulang_forwarded_by` bigint unsigned NULL,
  `approval_pulang_forwarded_at` timestamp NULL,
  `approval_pulang_approved_by` bigint unsigned NULL,
  `approval_pulang_reason` text NULL,
  `created_at` timestamp NULL,
  PRIMARY KEY (`id_absensi`),
  UNIQUE KEY `absensi_id_user_tanggal_unique` (`id_user`, `tanggal`),
  KEY `absensi_tanggal_status_index` (`tanggal`, `status`),
  KEY `absensi_user_tanggal_latest_idx` (`id_user`, `tanggal`, `id_absensi`),
  KEY `absensi_tanggal_latest_idx` (`tanggal`, `id_absensi`),
  KEY `absensi_status_idx` (`status`),
  KEY `absensi_approval_masuk_status_idx` (`approval_masuk_status`, `approval_masuk_requested_at`),
  KEY `absensi_approval_pulang_status_idx` (`approval_pulang_status`, `approval_pulang_requested_at`),
  KEY `absensi_id_periode_foreign` (`id_periode`),
  CONSTRAINT `absensi_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  CONSTRAINT `absensi_id_periode_foreign` FOREIGN KEY (`id_periode`) REFERENCES `periode` (`id_periode`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cuti` (
  `id_cuti` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint unsigned NOT NULL,
  `id_pengganti` bigint NULL,
  `id_periode` bigint unsigned NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `jenis_cuti` varchar(50) NOT NULL,
  `alasan` text NULL,
  `alasan_lainnya` text NULL,
  `alamat_cuti` text NULL,
  `dokumen_path` varchar(255) NULL,
  `admin_status` varchar(20) NOT NULL DEFAULT 'pending',
  `admin_approver_id` bigint unsigned NULL,
  `admin_processed_at` timestamp NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `approver_id` bigint unsigned NULL,
  `created_at` timestamp NULL,
  PRIMARY KEY (`id_cuti`),
  KEY `cuti_id_user_status_index` (`id_user`, `status`),
  KEY `cuti_user_tanggal_status_idx` (`id_user`, `tanggal_mulai`, `status`),
  KEY `cuti_status_latest_idx` (`status`, `id_cuti`),
  KEY `cuti_periode_idx` (`id_periode`),
  KEY `cuti_approver_id_foreign` (`approver_id`),
  KEY `cuti_id_pengganti_index` (`id_pengganti`),
  CONSTRAINT `cuti_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  CONSTRAINT `cuti_id_periode_foreign` FOREIGN KEY (`id_periode`) REFERENCES `periode` (`id_periode`) ON DELETE SET NULL,
  CONSTRAINT `cuti_approver_id_foreign` FOREIGN KEY (`approver_id`) REFERENCES `users` (`id_user`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tugas` (
  `id_tugas` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint unsigned NOT NULL,
  `id_periode` bigint unsigned NULL,
  `tanggal_mulai` datetime NOT NULL,
  `tanggal_selesai` datetime NULL,
  `uraian` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `submitted_at` timestamp NULL,
  `is_late_input` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL,
  PRIMARY KEY (`id_tugas`),
  KEY `tugas_id_user_status_index` (`id_user`, `status`),
  KEY `tugas_user_tanggal_status_idx` (`id_user`, `tanggal_mulai`, `status`),
  KEY `tugas_status_latest_idx` (`status`, `id_tugas`),
  KEY `tugas_periode_idx` (`id_periode`),
  KEY `tugas_periode_latest_idx` (`id_periode`, `id_tugas`),
  KEY `tugas_tanggal_latest_idx` (`tanggal_mulai`, `id_tugas`),
  CONSTRAINT `tugas_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  CONSTRAINT `tugas_id_periode_foreign` FOREIGN KEY (`id_periode`) REFERENCES `periode` (`id_periode`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sanksi` (
  `id_sanksi` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint unsigned NOT NULL,
  `jenis_sanksi` varchar(100) NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` text NULL,
  `acknowledged_at` timestamp NULL,
  PRIMARY KEY (`id_sanksi`),
  KEY `sanksi_id_user_tanggal_index` (`id_user`, `tanggal`),
  CONSTRAINT `sanksi_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notifikasi` (
  `id_notifikasi` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint unsigned NOT NULL,
  `judul` varchar(150) NOT NULL,
  `pesan` text NOT NULL,
  `tipe` varchar(50) NOT NULL DEFAULT 'system',
  `status_baca` tinyint(1) NOT NULL DEFAULT 0,
  `reference_id` bigint unsigned NULL,
  `reference_type` varchar(255) NULL,
  `created_at` timestamp NULL,
  PRIMARY KEY (`id_notifikasi`),
  KEY `notifikasi_id_user_status_baca_index` (`id_user`, `status_baca`),
  KEY `notifikasi_user_status_latest_idx` (`id_user`, `status_baca`, `id_notifikasi`),
  CONSTRAINT `notifikasi_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `kalender` (
  `id_kalender` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `nama_event` varchar(150) NULL,
  `jenis_event` varchar(50) NOT NULL,
  `keterangan` text NULL,
  PRIMARY KEY (`id_kalender`),
  KEY `kalender_tanggal_index` (`tanggal`),
  KEY `kalender_tanggal_latest_idx` (`tanggal`, `id_kalender`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `activity_log` (
  `id_log` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint unsigned NULL,
  `aktivitas` varchar(150) NOT NULL,
  `modul` varchar(100) NULL,
  `reference_id` bigint unsigned NULL,
  `reference_type` varchar(255) NULL,
  `status` varchar(30) NOT NULL DEFAULT 'success',
  `catatan` text NULL,
  `ip_address` varchar(45) NULL,
  `device` text NULL,
  `created_at` timestamp NULL,
  PRIMARY KEY (`id_log`),
  KEY `activity_log_id_user_created_at_index` (`id_user`, `created_at`),
  KEY `activity_log_user_modul_latest_idx` (`id_user`, `modul`, `id_log`),
  KEY `activity_log_modul_latest_idx` (`modul`, `id_log`),
  CONSTRAINT `activity_log_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_sensitive` (
  `id_sensitive` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint unsigned NOT NULL,
  `nik_encrypted` text NULL,
  `nik_hash` varchar(64) NULL,
  `no_hp_encrypted` text NULL,
  `no_hp_hash` varchar(64) NULL,
  `created_at` timestamp NULL,
  PRIMARY KEY (`id_sensitive`),
  UNIQUE KEY `user_sensitive_id_user_unique` (`id_user`),
  KEY `user_sensitive_nik_hash_index` (`nik_hash`),
  KEY `user_sensitive_no_hp_hash_index` (`no_hp_hash`),
  CONSTRAINT `user_sensitive_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pengaturan` (
  `kunci` varchar(50) NOT NULL,
  `nilai` text NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`kunci`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `shifts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_shift` varchar(50) NOT NULL,
  `jam_masuk` time NOT NULL,
  `jam_pulang` time NOT NULL,
  `durasi_jam` tinyint NOT NULL DEFAULT 8,
  `warna` varchar(7) NOT NULL DEFAULT '#3B82F6',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `urutan` int NOT NULL DEFAULT 0,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text NULL,
  `last_used_at` timestamp NULL,
  `expires_at` timestamp NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`, `tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`id_role`, `nama_role`) VALUES
  (1, 'Admin'),
  (2, 'Atasan'),
  (3, 'Petugas PPSU');

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
  (1, '2014_10_11_000000_create_master_tables', 1),
  (2, '2014_10_12_000000_create_users_table', 1),
  (3, '2014_10_12_100000_create_password_reset_tokens_table', 1),
  (4, '2019_08_19_000000_create_failed_jobs_table', 1),
  (5, '2019_12_14_000001_create_personal_access_tokens_table', 1),
  (6, '2026_05_08_000000_create_absensi_core_tables', 1),
  (7, '2026_05_08_045920_alter_status_enum_on_absensi_table', 1),
  (8, '2026_05_08_052439_add_foto_profil_to_users_table', 1),
  (9, '2026_05_09_133006_create_pengaturans_table', 1),
  (10, '2026_05_09_142031_add_details_to_cuti_table', 1),
  (11, '2026_05_14_000001_add_nik_hash_to_user_sensitive_table', 1),
  (12, '2026_05_18_000001_add_postgres_performance_indexes', 1),
  (13, '2026_05_18_000002_add_cuti_status_to_absensi_table', 1),
  (14, '2026_05_21_000001_add_revisi_petugas_admin_fields', 1),
  (15, '2026_05_21_000002_add_ketua_regu_workflow', 1),
  (16, '2026_05_22_004436_create_shifts_table', 1),
  (17, '2026_05_28_232707_add_approval_masuk_to_absensi_table', 1),
  (18, '2026_05_29_000501_add_late_input_fields_to_tugas_table', 1),
  (19, '2026_05_29_120000_add_approval_performance_indexes', 1),
  (20, '2026_05_29_121000_add_phone_to_user_sensitive_table', 1);

SET FOREIGN_KEY_CHECKS=1;
