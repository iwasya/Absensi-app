# Kebutuhan Sistem Aplikasi Absensi PPSU

Dokumen ini merangkum kebutuhan fungsional dan teknis dari sistem yang telah dibangun.

## 1. Kebutuhan Fungsional (Fitur Utama)

### A. Manajemen Pengguna & Keamanan
- **Multi-Role Access**: Mendukung peran Admin, Atasan (Manager/Menejer), dan Petugas.
- **Autentikasi**: Login aman dengan enkripsi password (Bcrypt).
- **Enkripsi Data Sensitif**: Perlindungan data NIK menggunakan enkripsi AES-256.
- **Log Aktivitas**: Pencatatan otomatis setiap tindakan user untuk audit sistem.

### B. Modul Absensi (Petugas)
- **Geofencing**: Validasi jarak absensi berdasarkan koordinat tempat tugas (radius 100m).
- **Foto Real-time**: Pengambilan foto saat masuk dan pulang langsung dari kamera perangkat.
- **Detail Absensi**: Riwayat lengkap dengan foto, koordinat map, dan alamat lokasi.
- **Buka Akses Khusus**: Admin dapat membuka akses bagi petugas yang terlambat.

### C. Modul Laporan Tugas (Petugas & Atasan)
- **Input Tugas**: Pelaporan kegiatan harian disertai bukti foto.
- **Monitoring Atasan**: Atasan dapat melakukan Approval atau Rejection terhadap laporan tugas.
- **Kalender Tugas**: Visualisasi jadwal kegiatan dan status tugas dalam bentuk kalender interaktif.

### D. Manajemen Cuti
- **Pengajuan Cuti**: Input tanggal, jenis cuti (Tahunan/Besar), dan alamat selama cuti.
- **Pendamping Pengganti**: Kewajiban memilih petugas pengganti dari daftar role petugas.
- **Approval Workflow**: Notifikasi otomatis ke Atasan saat ada pengajuan baru.
- **Cetak Surat Cuti**: Generate surat izin resmi dengan Kop Surat, nomor otomatis, dan stempel digital setelah disetujui.

### E. Kedisiplinan & Sanksi
- **Input Sanksi**: Atasan dapat memberikan sanksi sesuai kategori baku (Teguran, SP1-SP3, dll).
- **Notifikasi Real-time**: Petugas mendapatkan pemberitahuan instan saat menerima sanksi.

### F. Pelaporan & Output
- **Dashboard Statistik**: Ringkasan data cepat untuk setiap role.
- **Fitur Cetak (Print Ready)**: Output laporan dalam format siap cetak (A4) untuk Absensi, Tugas, Sanksi, dan Surat Cuti.
- **Export Data**: Kemampuan ekspor log aktivitas ke format CSV.

---

## 2. Kebutuhan Non-Fungsional

- **Performa**: Implementasi **Paginasi** dan **Chunking** untuk menangani beban data besar tanpa membuat aplikasi lambat.
- **UI/UX Modern**: Desain premium dengan dukungan **Dark Mode** dan **Light Mode** yang dapat dikustomisasi.
- **Responsivitas**: Tampilan adaptif yang nyaman digunakan baik di PC maupun Smartphone.
- **SEO & Navigasi**: Struktur heading yang benar dan breadcrumb untuk kemudahan navigasi.

---

## 3. Kebutuhan Teknis (Stack Teknologi)

- **Framework**: Laravel 10+ (PHP 8.1+).
- **Database**: MySQL atau MariaDB.
- **Web Server**: Apache atau Nginx.
- **Client Side**: HTML5 (Kamera & Geolocation API), CSS3 (Modern UI), JavaScript (Vanilla).
- **Hardware**: 
    - Server: Minimal 2GB RAM.
    - User Petugas: Smartphone dengan fitur Kamera dan GPS Aktif.
    - Admin/Atasan: PC/Laptop dengan browser modern (Chrome/Edge).
