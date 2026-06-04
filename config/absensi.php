<?php

/**
 * Pengaturan jam absensi dan batas jarak
 * Bisa juga disimpan di database (tabel pengaturan) jika diperlukan
 */

return [
    // Jam absen masuk
    'jam_masuk_buka' => '07:50:00',
    'jam_masuk_tutup' => '08:05:00',
    'jam_masuk_batas_telat' => '07:50:00', // Batas waktu tepat waktu, setelah ini dianggap telat

    // Jam absen pulang
    'jam_pulang_buka' => '15:50:00',
    'jam_pulang_tutup' => '23:59:59',
    'durasi_kerja_default_jam' => 8,

    // Batas jarak (meter) dari lokasi kantor
    'jarak_maks_meter' => 100,

    // Batas ukuran foto (bytes)
    'foto_maks_bytes' => 5 * 1024 * 1024, // 5MB

    // Kuota cuti tahunan
    'kuota_cuti_tahunan' => 12,

    // Batas jam absen otomatis tidak_absen (setelah jam ini, buat record tidak_absen)
    'batas_otomatis_tidak_absen' => '08:05:00',
];
