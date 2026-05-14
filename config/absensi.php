<?php

/**
 * Pengaturan jam absensi dan batas jarak
 * Bisa juga disimpan di database (tabel pengaturan) jika diperlukan
 */

return [
    // Jam absen masuk
    'jam_masuk_buka' => '06:00:00',
    'jam_masuk_tutup' => '07:15:00',

    // Jam absen pulang
    'jam_pulang_buka' => '16:00:00',
    'jam_pulang_tutup' => '23:59:59',

    // Batas jarak (meter) dari lokasi kantor
    'jarak_maks_meter' => 100,

    // Batas ukuran foto (bytes)
    'foto_maks_bytes' => 5 * 1024 * 1024, // 5MB

    // Kuota cuti tahunan
    'kuota_cuti_tahunan' => 12,

    // Batas jam absen otomatis tidak_absen (setelah jam ini, buat record tidak_absen)
    'batas_otomatis_tidak_absen' => '07:15:00',
];