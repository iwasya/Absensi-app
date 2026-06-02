# Buku Panduan Proyek Absensi PPSU

## 1. Ringkasan Aplikasi

Absensi PPSU adalah aplikasi web berbasis Laravel untuk mengelola kehadiran petugas, laporan tugas harian, cuti, sanksi, kalender kerja, dan approval berjenjang. Aplikasi ini dipakai oleh tiga kelompok utama:

- Admin
- Atasan atau Kasie Ekbang
- Petugas PPSU

Fitur utama aplikasi:

- Login berdasarkan role.
- Dashboard statistik sesuai role.
- Absensi masuk dan pulang dengan foto serta lokasi GPS.
- Validasi area kerja berdasarkan koordinat tempat tugas.
- Pengajuan absen masuk atau pulang yang terlewat.
- Approval pengajuan oleh ketua regu dan atasan.
- Pengajuan cuti dengan petugas pengganti.
- Laporan tugas harian.
- Kalender kegiatan, tugas, absensi, libur mingguan, dan cuti.
- Sanksi dan pengakuan sanksi oleh petugas.
- Manajemen user, tempat tugas, shift, periode, kalender, dan data sensitif.
- Log aktivitas untuk audit.

## 2. Teknologi Yang Digunakan

- Framework: Laravel
- Bahasa backend: PHP
- Database: PostgreSQL atau MySQL
- Frontend: Blade, CSS, JavaScript vanilla
- Build asset: Vite
- Autentikasi: session Laravel
- Kamera: HTML5 MediaDevices API
- Lokasi: Browser Geolocation API

## 3. Struktur Role

### Admin

Admin bertugas mengelola data dan konfigurasi utama aplikasi.

Menu utama Admin:

- Dashboard
- Users
- Tempat Tugas
- Periode
- Kalender
- Cuti
- Sanksi
- Sift atau shift
- Buka Akses Absen
- Data Sensitif
- Pengaturan
- Logs

Hak akses Admin:

- Membuat, mengubah, dan menghapus user.
- Mengatur role, tempat tugas, regu, shift, dan data sensitif user.
- Mengelola lokasi tempat tugas beserta latitude dan longitude.
- Mengelola periode aktif.
- Mengelola kalender libur, kegiatan, dan cuti bersama.
- Membuka akses absen telat.
- Menyetujui beberapa proses absensi tertentu.
- Melihat log aktivitas sistem.

### Atasan atau Kasie Ekbang

Atasan bertugas memonitor dan menyetujui aktivitas petugas.

Menu utama Atasan:

- Dashboard
- Absensi
- Cuti
- Tugas
- Regu
- Kalender
- Sanksi

Hak akses Atasan:

- Melihat absensi petugas.
- Menyetujui atau menolak pengajuan absen masuk.
- Menyetujui atau menolak pengajuan absen pulang.
- Menyetujui atau menolak cuti.
- Menyetujui atau menolak laporan tugas.
- Mengatur regu dan ketua regu.
- Memberikan sanksi.
- Mencetak laporan.

### Petugas PPSU

Petugas menggunakan aplikasi untuk aktivitas harian.

Menu utama Petugas:

- Dashboard
- Regu
- Absensi
- Approval Regu
- Cuti
- Tugas
- Kalender
- Sanksi

Hak akses Petugas:

- Absen masuk.
- Absen pulang.
- Melihat riwayat absensi pribadi.
- Mengajukan absen masuk atau pulang yang terlewat.
- Jika menjadi ketua regu, meneruskan atau menolak approval anggota.
- Mengajukan cuti.
- Menerima atau menolak tugas sebagai pengganti cuti.
- Input laporan tugas harian.
- Melihat kalender.
- Melihat dan mengakui sanksi.

## 4. Panduan Penggunaan Untuk Petugas

### Login

1. Buka halaman aplikasi.
2. Masukkan username dan password.
3. Klik login.
4. Sistem akan mengarahkan ke dashboard sesuai role.

### Absen Masuk

1. Buka menu `Absensi`.
2. Pastikan kamera dan lokasi perangkat aktif.
3. Klik `Buka Kamera`.
4. Ambil foto.
5. Tunggu latitude, longitude, dan lokasi terisi otomatis.
6. Pilih shift bila diperlukan.
7. Isi jam istirahat bila diperlukan.
8. Tambahkan keterangan jika ada.
9. Klik `Simpan Absen Masuk`.

Catatan lokasi:

- Jika lokasi belum terbaca, aktifkan izin lokasi browser.
- Jika muncul `GPS belum akurat`, aktifkan lokasi presisi pada perangkat dan tunggu beberapa saat.
- Jika muncul `Di luar area kantor`, sistem membaca posisi berada di luar radius tempat tugas.

### Absen Pulang

1. Buka menu `Absensi`.
2. Pastikan sudah absen masuk.
3. Buka kamera dan ambil foto pulang.
4. Tunggu lokasi terbaca otomatis.
5. Klik `Simpan Absen Pulang`.

### Pengajuan Absen Terlewat

Pengajuan dipakai saat petugas lupa absen masuk atau pulang.

1. Buka menu `Absensi`.
2. Pada data yang belum lengkap, isi alasan pengajuan.
3. Klik tombol pengajuan.
4. Tunggu proses approval sesuai alur sistem.

Alur umum:

- Petugas mengajukan alasan.
- Ketua regu dapat meneruskan atau menolak.
- Atasan melakukan approve atau reject.
- Jika approved, petugas dapat mengisi absen yang dibuka.

### Cuti

1. Buka menu `Cuti`.
2. Isi tanggal mulai dan tanggal selesai.
3. Pilih jenis cuti.
4. Isi alasan dan alamat selama cuti.
5. Pilih petugas pengganti jika diwajibkan.
6. Kirim pengajuan.
7. Tunggu persetujuan.

Jika petugas ditunjuk sebagai pengganti cuti:

1. Buka menu `Cuti`.
2. Lihat permintaan pengganti.
3. Pilih terima atau tolak.

### Tugas Harian

1. Buka menu `Tugas`.
2. Pilih `Input Tugas`.
3. Isi tanggal mulai, tanggal selesai jika ada, dan uraian tugas.
4. Simpan laporan.
5. Status akan menunggu approval atasan.

### Kalender Petugas

Menu `Kalender` menampilkan kalender dengan tanda warna seperti di dashboard:

- Hijau: hadir
- Kuning: telat
- Merah: tidak absen
- Merah muda: cuti
- Ungu: tugas
- Biru: event atau kalender
- Toska: libur mingguan
- Oranye: pengganti cuti

Klik tanggal untuk melihat detail pada tanggal tersebut.

### Sanksi

1. Buka menu `Sanksi`.
2. Lihat daftar sanksi yang diterima.
3. Jika ada sanksi yang belum diakui, klik tombol pengakuan.

## 5. Panduan Untuk Atasan

### Monitoring Absensi

1. Buka menu `Absensi`.
2. Gunakan filter bila tersedia.
3. Periksa data masuk, pulang, status, dan approval.
4. Cetak laporan jika diperlukan.

### Approval Absen Masuk dan Pulang

1. Buka menu `Absensi`.
2. Cari pengajuan yang berstatus pending.
3. Baca alasan pengajuan.
4. Pilih approve atau reject.

### Approval Cuti

1. Buka menu `Cuti`.
2. Lihat daftar cuti pending.
3. Periksa tanggal, jenis cuti, alasan, dan petugas pengganti.
4. Approve atau reject.

### Approval Tugas

1. Buka menu `Tugas`.
2. Periksa laporan tugas yang masuk.
3. Approve jika laporan benar.
4. Reject jika laporan perlu ditolak.
5. Gunakan fitur reminder jika petugas perlu diingatkan.

### Regu

Menu `Regu` digunakan untuk:

- Mengatur anggota regu.
- Mengatur ketua regu.
- Mengatur operasional regu.
- Mengatur hari libur mingguan petugas.

### Sanksi

1. Buka menu `Sanksi`.
2. Pilih petugas.
3. Isi jenis sanksi, tanggal, dan keterangan.
4. Simpan.
5. Petugas akan dapat melihat sanksi tersebut.

## 6. Panduan Untuk Admin

### Manajemen User

Menu `Users` digunakan untuk:

- Tambah user baru.
- Edit data user.
- Hapus user.
- Import user.
- Download template import user.
- Mengatur role, tempat tugas, regu, shift, dan data pendukung.

### Tempat Tugas

Menu `Tempat Tugas` digunakan untuk menentukan lokasi kerja.

Data penting:

- Nama tempat
- Alamat
- Latitude
- Longitude

Latitude dan longitude dipakai untuk geofencing absensi. Pastikan koordinat diambil dari titik lokasi yang benar.

### Periode

Menu `Periode` dipakai untuk mengatur periode aktif sistem.

Periode aktif mempengaruhi filter dan rekap data absensi.

### Kalender

Menu `Kalender` dipakai untuk membuat:

- Libur
- Kegiatan
- Cuti bersama

Kalender akan muncul pada dashboard, menu kalender, dan informasi harian.

### Shift

Menu `Sift` atau shift dipakai untuk:

- Membuat shift.
- Mengubah jam masuk dan jam pulang.
- Mengaktifkan atau menonaktifkan shift.
- Assign shift ke petugas.
- Assign shift secara massal.

### Buka Akses Absen

Menu ini dipakai saat admin perlu membuka akses absen khusus untuk petugas tertentu.

Contoh penggunaan:

- Petugas melewati jam absen masuk.
- Admin memberi akses khusus.
- Petugas bisa mengisi absen setelah akses dibuka.

### Data Sensitif

Menu ini dipakai untuk mengelola data sensitif user, seperti NIK dan data pribadi lain yang perlu dibatasi aksesnya.

### Pengaturan

Menu `Pengaturan` dipakai untuk konfigurasi aplikasi.

Contoh konfigurasi:

- Jam kerja.
- Ketentuan absensi.
- Parameter sistem lain.

### Logs

Menu `Logs` menampilkan aktivitas user untuk kebutuhan audit.

Admin dapat melihat aktivitas seperti:

- Login.
- Perubahan data.
- Pengajuan.
- Approval.
- Aksi penting lain.

## 7. Alur Bisnis Utama

### Alur Absensi Normal

1. Petugas login.
2. Petugas membuka menu absensi.
3. Sistem membaca kamera dan lokasi.
4. Petugas mengambil foto.
5. Sistem memvalidasi lokasi terhadap tempat tugas.
6. Sistem menyimpan jam masuk atau pulang.
7. Status absensi tercatat.

### Alur Absensi Terlewat

1. Petugas mengajukan alasan absen terlewat.
2. Ketua regu memeriksa pengajuan.
3. Ketua regu meneruskan atau menolak.
4. Atasan menyetujui atau menolak.
5. Jika disetujui, petugas dapat mengisi absen.

### Alur Cuti

1. Petugas mengajukan cuti.
2. Petugas memilih pengganti jika diperlukan.
3. Pengganti menerima atau menolak.
4. Atasan memproses pengajuan.
5. Status cuti diperbarui.
6. Jika disetujui, surat cuti dapat dicetak.

### Alur Tugas

1. Petugas input tugas harian.
2. Tugas masuk status pending.
3. Atasan memeriksa tugas.
4. Atasan approve atau reject.
5. Status tampil pada laporan dan kalender.

### Alur Sanksi

1. Atasan membuat sanksi.
2. Sanksi tampil pada menu petugas.
3. Petugas mengakui sanksi.
4. Sistem mencatat waktu pengakuan.

## 8. Struktur Folder Penting

- `app/Http/Controllers` berisi controller utama.
- `app/Models` berisi model database.
- `app/Services` berisi service pendukung.
- `app/Support` berisi helper internal seperti logger dan optimizer gambar.
- `resources/views` berisi tampilan Blade.
- `routes/web.php` berisi route aplikasi web.
- `database/migrations` berisi struktur database.
- `database/seeders` berisi data awal.
- `config/absensi.php` berisi konfigurasi dasar absensi.
- `public` berisi entry point dan asset publik.

## 9. Route Utama

Route umum:

- `/login`
- `/dashboard`
- `/notifikasi`
- `/profile`

Route petugas:

- `/petugas/regu`
- `/petugas/absensi`
- `/petugas/cuti`
- `/petugas/tugas/input`
- `/petugas/tugas/laporan`
- `/petugas/tugas/kalender`
- `/petugas/sanksi`

Route atasan:

- `/atasan/absensi`
- `/atasan/cuti`
- `/atasan/tugas`
- `/atasan/regu`
- `/atasan/kalender`
- `/atasan/sanksi`

Route admin:

- `/admin/users`
- `/admin/tempat`
- `/admin/periode`
- `/admin/kalender`
- `/admin/cuti`
- `/admin/sanksi`
- `/admin/sift`
- `/admin/buka-absen`
- `/admin/data-sensitif`
- `/admin/pengaturan`
- `/admin/logs`

## 10. Instalasi Lokal

### Prasyarat

- PHP sesuai versi Laravel project.
- Composer.
- Node.js dan npm.
- Database PostgreSQL atau MySQL.

### Langkah Instalasi

1. Install dependency PHP.

```bash
composer install
```

2. Install dependency frontend.

```bash
npm install
```

3. Buat file environment.

```bash
cp .env.example .env
```

Jika `.env.example` belum tersedia, buat `.env` berdasarkan kebutuhan lokal dan jangan membagikan credential asli.

4. Generate application key.

```bash
php artisan key:generate
```

5. Atur koneksi database pada `.env`.

Contoh PostgreSQL:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=absensi_app
DB_USERNAME=postgres
DB_PASSWORD=
```

Contoh MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=absensi_app
DB_USERNAME=root
DB_PASSWORD=
```

6. Jalankan migrasi.

```bash
php artisan migrate
```

7. Isi data shift awal jika diperlukan.

```bash
php artisan db:seed --class=ShiftSeeder
```

8. Jalankan server lokal.

```bash
php artisan serve
```

9. Jalankan Vite untuk asset frontend.

```bash
npm run dev
```

## 11. Menjalankan Test

Jalankan semua test:

```bash
php artisan test
```

Jalankan test tertentu:

```bash
php artisan test --filter ExampleTest
```

## 12. Pengaturan Absensi Dan Lokasi

Konfigurasi dasar absensi ada di `config/absensi.php`.

Contoh parameter:

- Jam masuk buka.
- Jam masuk tutup.
- Jam pulang buka.
- Jam pulang tutup.
- Radius maksimal lokasi.
- Maksimal ukuran foto.

Validasi lokasi memakai data latitude dan longitude dari tempat tugas. Browser harus mengirim lokasi yang cukup akurat. Jika akurasi GPS terlalu rendah, sistem akan meminta petugas mengaktifkan lokasi presisi.

## 13. Troubleshooting

### Login Gagal

- Pastikan username benar.
- Pastikan password benar.
- Pastikan user aktif dan memiliki role.
- Cek koneksi database.

### Kamera Tidak Bisa Dibuka

- Pastikan browser mengizinkan kamera.
- Gunakan browser modern seperti Chrome atau Edge.
- Pastikan perangkat memiliki kamera.
- Jika di production, gunakan HTTPS agar browser mengizinkan kamera.

### Lokasi Tidak Terbaca

- Pastikan izin lokasi browser aktif.
- Aktifkan GPS perangkat.
- Aktifkan lokasi presisi.
- Tunggu beberapa detik sampai GPS stabil.
- Pastikan aplikasi diakses melalui domain yang mendukung geolocation.

### Muncul GPS Belum Akurat

- Pindah ke area terbuka.
- Aktifkan lokasi presisi di perangkat.
- Matikan mode hemat daya yang membatasi GPS.
- Reload halaman dan coba lagi.

### Di Luar Area Kantor

- Pastikan petugas berada di lokasi tugas.
- Pastikan koordinat tempat tugas di admin benar.
- Cek latitude dan longitude tempat tugas.
- Cek radius maksimal di `config/absensi.php`.

### Data Kalender Tidak Muncul

- Pastikan kalender dibuat oleh admin.
- Pastikan tanggal event sesuai.
- Pastikan data absensi atau tugas berada pada bulan yang sedang dilihat.
- Pastikan periode dan tanggal tidak salah.

### Perubahan Tampilan Tidak Muncul

Jalankan:

```bash
php artisan optimize:clear
```

Jika memakai Vite:

```bash
npm run dev
```

## 14. Keamanan

Hal penting yang perlu dijaga:

- Jangan commit file `.env` yang berisi credential asli.
- Jangan membagikan password database.
- Gunakan HTTPS pada server production.
- Set `APP_DEBUG=false` pada production.
- Gunakan `SESSION_SECURE_COOKIE=true` pada HTTPS production.
- Rotasi password database jika pernah terekspos.
- Batasi akses menu admin hanya untuk user role admin.
- Periksa log aktivitas secara berkala.

Contoh konfigurasi production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-aplikasi.example
FORCE_HTTPS=true
SESSION_SECURE_COOKIE=true
```

## 15. Checklist Sebelum Production

- Database production siap.
- `.env` production sudah benar.
- `APP_DEBUG=false`.
- HTTPS aktif.
- Storage link dibuat bila diperlukan.
- Migrasi database selesai.
- User admin awal tersedia.
- Koordinat tempat tugas sudah valid.
- Shift sudah dibuat dan di-assign.
- Periode aktif sudah diset.
- Kalender libur sudah diinput.
- Test absensi kamera dan GPS dari perangkat petugas.
- Test approval atasan.
- Test cetak laporan.

## 16. Catatan Maintenance

Aktivitas rutin yang disarankan:

- Backup database secara berkala.
- Cek log aplikasi.
- Cek log aktivitas user.
- Update data user ketika ada mutasi.
- Perbarui kalender libur setiap periode.
- Validasi koordinat tempat tugas setelah perubahan lokasi kerja.
- Jalankan test setelah perubahan kode.

## 17. Kontak Dan Dukungan Internal

Isi bagian ini sesuai kebutuhan organisasi:

- Penanggung jawab aplikasi:
- Admin teknis:
- Kontak darurat:
- Prosedur eskalasi:

