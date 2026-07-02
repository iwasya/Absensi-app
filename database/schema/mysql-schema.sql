-- MySQL schema baseline for Absensi PPSU.
-- Target: MySQL 8.0+ / MariaDB 10.6+.
-- Usage:
--   mysql -u root -p -e "CREATE DATABASE absensi_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
--   mysql -u root -p absensi_app < database/schema/mysql-schema.sql
--   cp .env.mysql.example .env
--   php artisan key:generate
--
-- This file mirrors the public PostgreSQL schema from absensi_backup.dump.
-- It includes the migrations table so old migrations are not rerun after
-- importing the baseline schema. Future migrations will still run normally.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `personal_access_tokens`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `failed_jobs`;
DROP TABLE IF EXISTS `user_sensitive`;
DROP TABLE IF EXISTS `activity_log`;
DROP TABLE IF EXISTS `kalender`;
DROP TABLE IF EXISTS `libur_kompensasi`;
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
  `id_role` int unsigned NOT NULL AUTO_INCREMENT,
  `nama_role` varchar(50) NOT NULL,
  PRIMARY KEY (`id_role`),
  UNIQUE KEY `roles_nama_role_key` (`nama_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tempat_tugas` (
  `id_tempat` int unsigned NOT NULL AUTO_INCREMENT,
  `nama_tempat` varchar(150) NOT NULL,
  `alamat` text NULL,
  `latitude` decimal(10,8) NULL,
  `longitude` decimal(11,8) NULL,
  PRIMARY KEY (`id_tempat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id_user` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(150) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(150) NOT NULL,
  `foto_profil` varchar(255) NULL,
  `id_role` int unsigned NOT NULL,
  `id_tempat` int unsigned NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `regu` varchar(20) NULL,
  `shift` varchar(30) NULL,
  `status_aktif` varchar(20) NOT NULL DEFAULT 'aktif',
  `no_hp` varchar(30) NULL,
  `alamat` text NULL,
  `jabatan` varchar(100) NULL,
  `is_ketua_regu` tinyint(1) NOT NULL DEFAULT 0,
  `hari_libur` smallint unsigned NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `users_email_key` (`email`),
  UNIQUE KEY `users_username_key` (`username`),
  KEY `idx_users_id_role` (`id_role`),
  KEY `idx_users_id_tempat` (`id_tempat`),
  KEY `users_created_at_idx` (`created_at`),
  KEY `users_regu_ketua_idx` (`regu`, `is_ketua_regu`),
  KEY `users_role_idx` (`id_role`),
  KEY `users_status_role_idx` (`status_aktif`, `id_role`),
  KEY `users_tempat_idx` (`id_tempat`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id_role`),
  CONSTRAINT `users_ibfk_2` FOREIGN KEY (`id_tempat`) REFERENCES `tempat_tugas` (`id_tempat`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `periode` (
  `id_periode` int unsigned NOT NULL AUTO_INCREMENT,
  `nama_periode` varchar(100) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status` varchar(50) NULL DEFAULT 'aktif',
  PRIMARY KEY (`id_periode`),
  KEY `periode_aktif_tanggal_idx` (`status`, `tanggal_mulai`, `tanggal_selesai`, `id_periode` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `absensi` (
  `id_absensi` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint unsigned NOT NULL,
  `id_periode` int unsigned NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time NULL,
  `foto_masuk` varchar(255) NULL,
  `latitude_masuk` decimal(10,8) NULL,
  `longitude_masuk` decimal(11,8) NULL,
  `lokasi_masuk` varchar(255) NULL,
  `jam_pulang` time NULL,
  `foto_pulang` varchar(255) NULL,
  `latitude_pulang` decimal(10,8) NULL,
  `longitude_pulang` decimal(11,8) NULL,
  `lokasi_pulang` varchar(255) NULL,
  `status` varchar(30) NULL DEFAULT 'hadir',
  `keterangan` text NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `shift` varchar(30) NULL,
  `jam_istirahat_mulai` time NULL,
  `jam_istirahat_selesai` time NULL,
  `approval_pulang_status` varchar(30) NULL,
  `approval_pulang_requested_at` timestamp NULL,
  `approval_pulang_approved_by` bigint unsigned NULL,
  `approval_pulang_reason` text NULL,
  `approval_pulang_forwarded_by` bigint unsigned NULL,
  `approval_pulang_forwarded_at` timestamp NULL,
  `approval_masuk_status` varchar(30) NULL,
  `approval_masuk_requested_at` timestamp NULL,
  `approval_masuk_forwarded_by` bigint unsigned NULL,
  `approval_masuk_forwarded_at` timestamp NULL,
  `approval_masuk_approved_by` bigint unsigned NULL,
  `approval_masuk_reason` text NULL,
  PRIMARY KEY (`id_absensi`),
  UNIQUE KEY `absensi_id_user_tanggal_key` (`id_user`, `tanggal`),
  KEY `absensi_approval_masuk_status_idx` (`approval_masuk_status`, `approval_masuk_requested_at`),
  KEY `absensi_approval_pulang_status_idx` (`approval_pulang_status`, `approval_pulang_requested_at`),
  KEY `absensi_status_idx` (`status`),
  KEY `absensi_tanggal_latest_idx` (`tanggal` DESC, `id_absensi` DESC),
  KEY `absensi_user_tanggal_latest_idx` (`id_user`, `tanggal` DESC, `id_absensi` DESC),
  KEY `idx_absensi_id_periode` (`id_periode`),
  CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  CONSTRAINT `absensi_ibfk_2` FOREIGN KEY (`id_periode`) REFERENCES `periode` (`id_periode`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cuti` (
  `id_cuti` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint unsigned NOT NULL,
  `id_pengganti` bigint unsigned NULL,
  `id_periode` int unsigned NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `jenis_cuti` varchar(100) NULL,
  `alasan` text NULL,
  `alasan_lainnya` text NULL,
  `alamat_cuti` text NULL,
  `status` varchar(50) NULL DEFAULT 'pending',
  `approver_id` bigint unsigned NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `dokumen_path` varchar(255) NULL,
  `admin_status` varchar(20) NOT NULL DEFAULT 'pending',
  `admin_approver_id` bigint unsigned NULL,
  `admin_processed_at` timestamp NULL,
  `replacement_status` varchar(20) NOT NULL DEFAULT 'pending',
  `replacement_confirmed_at` timestamp NULL,
  `replacement_note` text NULL,
  PRIMARY KEY (`id_cuti`),
  KEY `cuti_pengganti_tanggal_status_idx` (`id_pengganti`, `tanggal_mulai`, `tanggal_selesai`, `status`),
  KEY `cuti_periode_idx` (`id_periode`),
  KEY `cuti_replacement_status_idx` (`replacement_status`, `id_pengganti`),
  KEY `cuti_status_latest_idx` (`status`, `id_cuti` DESC),
  KEY `cuti_user_tanggal_status_idx` (`id_user`, `tanggal_mulai` DESC, `status`),
  KEY `idx_cuti_approver_id` (`approver_id`),
  KEY `idx_cuti_id_periode` (`id_periode`),
  KEY `idx_cuti_id_user` (`id_user`),
  CONSTRAINT `cuti_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  CONSTRAINT `cuti_ibfk_2` FOREIGN KEY (`approver_id`) REFERENCES `users` (`id_user`) ON DELETE SET NULL,
  CONSTRAINT `cuti_ibfk_3` FOREIGN KEY (`id_periode`) REFERENCES `periode` (`id_periode`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tugas` (
  `id_tugas` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint unsigned NOT NULL,
  `id_periode` int unsigned NULL,
  `tanggal_mulai` datetime NOT NULL,
  `tanggal_selesai` datetime NULL,
  `uraian` text NOT NULL,
  `status` varchar(50) NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `submitted_at` timestamp NULL,
  `is_late_input` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_tugas`),
  KEY `idx_tugas_id_periode` (`id_periode`),
  KEY `idx_tugas_id_user` (`id_user`),
  KEY `tugas_periode_idx` (`id_periode`),
  KEY `tugas_periode_latest_idx` (`id_periode`, `id_tugas`),
  KEY `tugas_status_latest_idx` (`status`, `id_tugas` DESC),
  KEY `tugas_tanggal_latest_idx` (`tanggal_mulai`, `id_tugas`),
  KEY `tugas_user_late_input_idx` (`id_user`, `is_late_input`),
  KEY `tugas_user_tanggal_status_idx` (`id_user`, `tanggal_mulai` DESC, `status`),
  CONSTRAINT `tugas_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  CONSTRAINT `tugas_ibfk_2` FOREIGN KEY (`id_periode`) REFERENCES `periode` (`id_periode`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sanksi` (
  `id_sanksi` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint unsigned NOT NULL,
  `jenis_sanksi` varchar(100) NULL,
  `tanggal` date NULL,
  `keterangan` text NULL,
  `acknowledged_at` timestamp NULL,
  PRIMARY KEY (`id_sanksi`),
  KEY `idx_sanksi_id_user` (`id_user`),
  CONSTRAINT `sanksi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notifikasi` (
  `id_notifikasi` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint unsigned NOT NULL,
  `judul` varchar(150) NULL,
  `pesan` text NULL,
  `tipe` varchar(50) NULL,
  `status_baca` smallint NULL DEFAULT 0,
  `reference_id` bigint unsigned NULL,
  `reference_type` varchar(50) NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_notifikasi`),
  KEY `idx_notifikasi_id_user` (`id_user`),
  KEY `notifikasi_user_status_latest_idx` (`id_user`, `status_baca`, `id_notifikasi` DESC),
  CONSTRAINT `notifikasi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `kalender` (
  `id_kalender` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `nama_event` varchar(150) NULL,
  `jenis_event` varchar(50) NULL,
  `keterangan` text NULL,
  PRIMARY KEY (`id_kalender`),
  UNIQUE KEY `kalender_tanggal_nama_event_key` (`tanggal`, `nama_event`),
  KEY `kalender_tanggal_latest_idx` (`tanggal`, `id_kalender`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `libur_kompensasi` (
  `id_libur_kompensasi` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint unsigned NOT NULL,
  `id_cuti` bigint unsigned NOT NULL,
  `tanggal_kerja` date NOT NULL,
  `tanggal_dipakai` date NULL,
  `status` varchar(20) NOT NULL DEFAULT 'tersedia',
  `keterangan` text NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id_libur_kompensasi`),
  UNIQUE KEY `libur_kompensasi_user_cuti_tanggal_unique` (`id_user`, `id_cuti`, `tanggal_kerja`),
  KEY `libur_kompensasi_cuti_idx` (`id_cuti`),
  KEY `libur_kompensasi_user_status_idx` (`id_user`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `activity_log` (
  `id_log` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint unsigned NOT NULL,
  `aktivitas` varchar(100) NOT NULL,
  `modul` varchar(50) NULL,
  `reference_id` bigint unsigned NULL,
  `reference_type` varchar(50) NULL,
  `status` varchar(50) NULL,
  `catatan` text NULL,
  `ip_address` varchar(45) NULL,
  `device` varchar(150) NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_log`),
  KEY `activity_log_modul_latest_idx` (`modul`, `id_log` DESC),
  KEY `activity_log_user_modul_latest_idx` (`id_user`, `modul`, `id_log` DESC),
  KEY `idx_activity_log_id_user` (`id_user`),
  CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_sensitive` (
  `id_sensitive` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint unsigned NOT NULL,
  `nik_encrypted` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `nik_hash` varchar(64) NULL,
  `no_hp_encrypted` text NULL,
  `no_hp_hash` varchar(64) NULL,
  PRIMARY KEY (`id_sensitive`),
  UNIQUE KEY `user_sensitive_id_user_key` (`id_user`),
  KEY `user_sensitive_nik_hash_index` (`nik_hash`),
  KEY `user_sensitive_no_hp_hash_index` (`no_hp_hash`),
  CONSTRAINT `user_sensitive_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE
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
  `durasi_jam` smallint NOT NULL DEFAULT 8,
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
  UNIQUE KEY `failed_jobs_uuid_key` (`uuid`)
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
  UNIQUE KEY `personal_access_tokens_token_key` (`token`),
  KEY `idx_personal_access_tokens_personal_access_tokens_tokenable_typ` (`tokenable_type`, `tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`id_role`, `nama_role`) VALUES
  (1, 'Petugas PPSU'),
  (2, 'Admin Absensi'),
  (3, 'Atasan');

INSERT INTO `shifts` (`id`, `nama_shift`, `jam_masuk`, `jam_pulang`, `durasi_jam`, `warna`, `status`, `urutan`, `created_at`, `updated_at`) VALUES
  (1, 'Shift 1', '05:00:00', '13:00:00', 8, '#3B82F6', 1, 1, '2026-05-22 00:48:09', '2026-05-28 01:08:55'),
  (2, 'Shift 2', '07:00:00', '15:00:00', 8, '#10B981', 1, 2, '2026-05-22 00:48:10', '2026-05-26 13:58:20'),
  (3, 'Shift 3', '17:00:00', '07:00:00', 14, '#8B5CF6', 1, 3, '2026-05-22 00:48:10', '2026-05-26 13:58:20');

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
  (1, '2014_10_12_000000_create_users_table', 1),
  (2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
  (3, '2019_08_19_000000_create_failed_jobs_table', 1),
  (4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
  (5, '2026_05_08_045920_alter_status_enum_on_absensi_table', 1),
  (6, '2026_05_08_052439_add_foto_profil_to_users_table', 2),
  (7, '2026_05_09_133006_create_pengaturans_table', 3),
  (8, '2026_05_09_142031_add_details_to_cuti_table', 4),
  (9, '2014_10_11_000000_create_master_tables', 5),
  (10, '2026_05_08_000000_create_absensi_core_tables', 5),
  (11, '2026_05_14_000001_add_nik_hash_to_user_sensitive_table', 5),
  (12, '2026_05_18_000001_add_postgres_performance_indexes', 6),
  (13, '2026_05_18_000002_add_cuti_status_to_absensi_table', 7),
  (14, '2026_05_21_000001_add_revisi_petugas_admin_fields', 7),
  (15, '2026_05_21_000002_add_ketua_regu_workflow', 8),
  (16, '2026_05_22_004436_create_shifts_table', 9),
  (17, '2026_05_28_232707_add_approval_masuk_to_absensi_table', 10),
  (18, '2026_05_29_000501_add_late_input_fields_to_tugas_table', 11),
  (19, '2026_05_29_120000_add_approval_performance_indexes', 12),
  (20, '2026_05_29_121000_add_phone_to_user_sensitive_table', 13),
  (21, '2026_05_30_000001_add_hari_libur_to_users_table', 14),
  (22, '2026_05_30_000002_add_replacement_confirmation_to_cuti', 15),
  (23, '2026_05_30_000003_add_cuti_replacement_indexes', 16),
  (24, '2026_06_09_000001_add_tugas_late_input_index', 17);

SET FOREIGN_KEY_CHECKS=1;
