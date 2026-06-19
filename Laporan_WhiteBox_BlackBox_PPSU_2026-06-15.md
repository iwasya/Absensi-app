# Laporan White Box dan Black Box Testing PPSU

Tanggal laporan: 2026-06-19  
Tanggal eksekusi pengujian terakhir: 2026-06-19  
Target pengujian black box: https://absensippsu.myftp.biz/login  
Dokumen acuan: `Tabel_WhiteBox_BlackBox_PPSU.docx`

## Ringkasan Eksekusi

| Jenis Testing | Metode | Hasil |
|---|---|---|
| White box | `XDG_CONFIG_HOME=/tmp php artisan test` pada proyek lokal | Lulus: 194 tests passed, 377 assertions |
| Black box | HTTP live ke `https://absensippsu.myftp.biz` memakai CSRF token dan cookie session | Lulus untuk akun admin, atasan, dan petugas berbasis email; akses silang role ditolak `403` |
| Catatan kredensial | Login petugas diuji dengan email dan username | `iwasya22@gmail.com` lulus, `iwasya22` gagal login pada web live |

## Ringkasan Kredensial Yang Dipakai

| Role | Login yang Diuji | Password | Hasil Web Live |
|---|---|---:|---|
| Admin | `admin@local.test` | disamarkan | Lulus, redirect ke `/dashboard` |
| Atasan | `atasan@local.test` | disamarkan | Lulus, redirect ke `/dashboard` |
| Petugas | `iwasya22@gmail.com` | disamarkan | Lulus, redirect ke `/dashboard` |
| Petugas | `iwasya22` | disamarkan | Gagal login, redirect kembali ke `/login` |

Catatan: petugas berhasil pada web live ketika memakai email `iwasya22@gmail.com`. Username `iwasya22` belum dapat dipakai untuk login live pada hasil pengujian ini.

## White Box Testing

White box dilakukan dengan membaca struktur proyek lokal, route, middleware, controller, service, dan automated test Laravel. Bagian ini menilai isi kode, bukan hanya tampilan dari luar.

| No | Komponen | Hal yang Diperiksa | Hasil Aktual |
|---:|---|---|---|
| 1 | `routes/web.php` | Route auth, petugas, atasan, admin, throttle, dan middleware role | Sesuai. Route `/login` memakai `guest`; route protected berada dalam group `auth`; area petugas/atasan/admin memakai middleware role; action sensitif memakai proteksi request. |
| 2 | `EnsureRole` middleware | Pembatasan akses per role | Sesuai. Middleware menolak akses role yang tidak sesuai dengan response `403`. Feature test `RoleAccessTest` lulus. |
| 3 | `AuthTest` | Login dengan email, username, NIK, invalid login, logout, session regeneration, dan dashboard | Sesuai. Semua skenario auth lokal lulus. |
| 4 | `AdminController` dan user management | CRUD user, validasi username/email/role/NIK, upload foto profil opsional, import template, bulk delete | Sesuai. Feature test `AdminCreateUserTest` dan `AdminUserManagementTest` lulus. |
| 5 | `AbsensiController` | Validasi absensi, foto, face verification, approval masuk/pulang, lokasi, detail, dan print | Sesuai. Feature test `AbsensiTest` lulus. |
| 6 | `AtasanApprovalTest` | Approval absensi, cuti, tugas, regu, kalender, export, dan print | Sesuai. Semua skenario atasan lulus. |
| 7 | `CutiReplacementService` dan `CutiTest` | Pengajuan cuti, validasi pengganti, approval blocker, accept/reject pengganti, print cuti | Sesuai. Unit dan feature test cuti lulus. |
| 8 | `FaceVerificationService` | Disabled mode, mismatch response, decode data URL, dan placeholder endpoint unavailable | Sesuai. Unit test service lulus. |
| 9 | Automated Test Laravel | `XDG_CONFIG_HOME=/tmp php artisan test` | Lulus penuh. Hasil aktual: 194 passed, 377 assertions, 0 failed. |

### Hasil Command White Box

```text
Tests:    194 passed (377 assertions)
Duration: 13.35s
```

## Black Box Testing Web Live

Black box dilakukan pada web live `https://absensippsu.myftp.biz/login` menggunakan HTTP request dengan CSRF token dan cookie session. Fokusnya adalah perilaku dari luar: redirect, login, akses halaman sesuai role, dan penolakan akses silang role.

### Unauthenticated

| No | Area | Kasus Uji | Expected | Actual | Status |
|---:|---|---|---|---|---|
| 1 | Unauthenticated | GET `/` | Redirect ke `/login` | `302` | Lulus |
| 2 | Unauthenticated | GET `/dashboard` | Redirect ke `/login` | `302` | Lulus |
| 3 | Unauthenticated | GET `/admin/users` | Redirect ke `/login` | `302` | Lulus |
| 4 | Unauthenticated | GET `/petugas/absensi` | Redirect ke `/login` | `302` | Lulus |
| 5 | Unauthenticated | GET `/atasan/absensi` | Redirect ke `/login` | `302` | Lulus |

### Admin

| No | Area | Kasus Uji | Expected | Actual | Status |
|---:|---|---|---|---|---|
| 6 | Admin | Login dengan `admin@local.test` | Masuk dashboard | POST login `302` ke `/dashboard` | Lulus |
| 7 | Admin | Akses `/dashboard` | HTTP `200` | `200` | Lulus |
| 8 | Admin | Akses `/admin/users` | HTTP `200` | `200` | Lulus |
| 9 | Admin | Akses `/admin/pengaturan` | HTTP `200` | `200` | Lulus |
| 10 | Admin | Akses silang `/petugas/absensi` | HTTP `403` | `403` | Lulus |
| 11 | Admin | Akses silang `/atasan/absensi` | HTTP `403` | `403` | Lulus |

### Atasan

| No | Area | Kasus Uji | Expected | Actual | Status |
|---:|---|---|---|---|---|
| 12 | Atasan | Login dengan `atasan@local.test` | Masuk dashboard | POST login `302` ke `/dashboard` | Lulus |
| 13 | Atasan | Akses `/dashboard` | HTTP `200` | `200` | Lulus |
| 14 | Atasan | Akses `/atasan/absensi` | HTTP `200` | `200` | Lulus |
| 15 | Atasan | Akses `/atasan/tugas` | HTTP `200` | `200` | Lulus |
| 16 | Atasan | Akses silang `/admin/users` | HTTP `403` | `403` | Lulus |
| 17 | Atasan | Akses silang `/petugas/absensi` | HTTP `403` | `403` | Lulus |

### Petugas

| No | Area | Kasus Uji | Expected | Actual | Status |
|---:|---|---|---|---|---|
| 18 | Petugas | Login dengan email `iwasya22@gmail.com` | Masuk dashboard | POST login `302` ke `/dashboard` | Lulus |
| 19 | Petugas | Akses `/dashboard` | HTTP `200` | `200` | Lulus |
| 20 | Petugas | Akses `/petugas/absensi` | HTTP `200` | `200` | Lulus |
| 21 | Petugas | Akses `/petugas/tugas/input` | HTTP `200` | `200` | Lulus |
| 22 | Petugas | Akses silang `/admin/users` | HTTP `403` | `403` | Lulus |
| 23 | Petugas | Akses silang `/atasan/absensi` | HTTP `403` | `403` | Lulus |
| 24 | Petugas | Login dengan username `iwasya22` | Masuk dashboard | POST login `302` kembali ke `/login`; GET `/dashboard` tetap `302` | Gagal |

### Invalid Login

| No | Area | Kasus Uji | Expected | Actual | Status |
|---:|---|---|---|---|---|
| 25 | Auth | Login invalid setelah rangkaian percobaan | Tidak masuk dashboard | POST login `429`; GET `/dashboard` `302` | Lulus dengan catatan throttle aktif |

Catatan: response `429` pada invalid login menunjukkan rate limiter/throttle aktif setelah beberapa percobaan login berurutan. Secara fungsi keamanan, user invalid tetap tidak mendapat session dan tetap diarahkan ke login ketika mengakses `/dashboard`.

## File Pendukung Yang Digunakan

| File | Fungsi |
|---|---|
| `tests/e2e/run_blackbox.sh` | Runner black-box HTTP live berbasis `curl`, CSRF token, dan cookie jar. |
| `tests/e2e/staging_blackbox_http.sh` | Runner black-box staging yang lebih terstruktur untuk CI/manual. |
| `tests/Feature/AuthTest.php` | Acuan lokal untuk login email/username/NIK, invalid login, logout, session, dan dashboard. |
| `tests/Feature/RoleAccessTest.php` | Acuan lokal untuk pembatasan akses role. |
| `tests/Feature/AbsensiTest.php` | Acuan lokal untuk perilaku absensi dan approval petugas. |
| `tests/Feature/AdminUserManagementTest.php` | Acuan lokal untuk CRUD dan validasi user admin. |
| `tests/Feature/AtasanApprovalTest.php` | Acuan lokal untuk approval atasan, tugas, regu, kalender, export, dan print. |

## Kesimpulan

Berdasarkan white-box testing lokal, suite Laravel sudah lulus penuh dengan 194 test dan 377 assertion. Route, middleware role, autentikasi, user management, absensi, cuti, approval atasan, notifikasi, profile, sanksi, dan service pendukung berjalan sesuai automated test.

Berdasarkan black-box testing web live, akun `admin@local.test`, `atasan@local.test`, dan `iwasya22@gmail.com` berhasil login dan hanya dapat mengakses halaman sesuai rolenya. Akses silang antar-role ditolak dengan `403`, sedangkan akses unauthenticated diarahkan ke login dengan `302`.

Temuan utama black-box adalah username `iwasya22` gagal login pada web live, sehingga untuk testing live petugas perlu memakai email `iwasya22@gmail.com`. Invalid login tidak mendapat session; pada eksekusi beruntun server mengembalikan `429`, yang menunjukkan throttle login aktif.
