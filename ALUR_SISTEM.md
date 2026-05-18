# ALUR SISTEM APLIKASI ABSENSI

## 1. OVERVIEW SISTEM

Aplikasi Absensi adalah sistem manajemen kehadiran berbasis web yang dibangun dengan Laravel. Sistem ini mengelola absensi karyawan, pengajuan cuti, penugasan, dan sanksi dengan sistem approval berjenjang.

### Teknologi Stack
- **Framework**: Laravel (PHP)
- **Database**: MySQL/PostgreSQL
- **Frontend**: Blade Template, Vite
- **Authentication**: Laravel Sanctum

---

## 2. STRUKTUR ROLE & HAK AKSES

### Role dalam Sistem:

#### 1. **Admin**
   - Mengelola master data (users, tempat tugas, periode)
   - Mengelola kalender libur
   - Melihat semua sanksi
   - Mengatur akses buka absen
   - Mengelola data sensitif
   - Melihat activity logs
   - Mengatur pengaturan sistem

#### 2. **Atasan/Manager**
   - Melihat absensi bawahan
   - Approve/reject pengajuan cuti
   - Approve/reject laporan tugas
   - Memberikan sanksi kepada bawahan
   - Melihat kalender tugas
   - Cetak laporan absensi dan sanksi

#### 3. **Petugas/Karyawan**
   - Absen masuk dan pulang (dengan foto & lokasi GPS)
   - Mengajukan cuti
   - Input laporan tugas harian
   - Melihat riwayat absensi sendiri
   - Melihat sanksi yang diterima
   - Melihat kalender tugas

---

## 3. ALUR PROSES BISNIS UTAMA

### A. ALUR AUTENTIKASI

```
[User] → Login (username/password)
   ↓
[AuthController] → Validasi kredensial
   ↓
[Middleware Auth] → Cek session
   ↓
[Middleware Role] → Cek role user
   ↓
[Dashboard] → Redirect sesuai role
```

**Detail Proses:**
1. User mengakses `/login`
2. Input username & password
3. AuthController memvalidasi dengan database
4. Jika valid, buat session dan redirect ke dashboard
5. Middleware memeriksa role untuk akses fitur

---

### B. ALUR ABSENSI (Petugas)

```
[Petugas] → Buka halaman absensi
   ↓
[Sistem] → Cek sudah absen hari ini?
   ↓
   ├─ Belum absen masuk → Tampilkan tombol "Absen Masuk"
   ├─ Sudah absen masuk → Tampilkan tombol "Absen Pulang"
   └─ Sudah lengkap → Tampilkan status selesai
   ↓
[Petugas] → Klik tombol absen
   ↓
[Browser] → Minta izin kamera & lokasi GPS
   ↓
[Petugas] → Ambil foto selfie
   ↓
[Sistem] → Simpan data:
   - Tanggal & jam
   - Foto (foto_masuk/foto_pulang)
   - Koordinat GPS (latitude, longitude)
   - Lokasi (alamat dari reverse geocoding)
   - Status (hadir/terlambat/dll)
   ↓
[Database] → Record tersimpan di tabel absensi
   ↓
[Notifikasi] → Kirim notifikasi ke atasan
```

**Validasi Absensi:**
- Cek apakah dalam periode aktif
- Cek apakah sudah absen di hari yang sama
- Validasi lokasi GPS (opsional: radius dari kantor)
- Tentukan status (hadir/terlambat) berdasarkan jam

---

### C. ALUR PENGAJUAN CUTI

```
[Petugas] → Buka halaman cuti
   ↓
[Petugas] → Isi form pengajuan:
   - Tanggal mulai & selesai
   - Jenis cuti (sakit/tahunan/dll)
   - Alasan
   - Alamat selama cuti
   - Pilih pengganti
   ↓
[CutiController] → Validasi:
   - Cek bentrok dengan cuti lain
   - Cek kuota cuti (jika ada)
   - Validasi tanggal
   ↓
[Database] → Simpan dengan status "pending"
   ↓
[Notifikasi] → Kirim notifikasi ke atasan
   ↓
[Atasan] → Buka halaman approval cuti
   ↓
[Atasan] → Review pengajuan
   ↓
   ├─ Approve → Status = "disetujui"
   │   ↓
   │   [Notifikasi] → Kirim ke petugas (approved)
   │
   └─ Reject → Status = "ditolak"
       ↓
       [Notifikasi] → Kirim ke petugas (rejected)
```

**Status Cuti:**
- `pending` - Menunggu approval
- `disetujui` - Disetujui atasan
- `ditolak` - Ditolak atasan

---

### D. ALUR PENUGASAN

```
[Petugas] → Input tugas harian
   ↓
[Form Input]:
   - Tanggal mulai & selesai
   - Uraian tugas
   ↓
[TugasController] → Simpan dengan status "pending"
   ↓
[Database] → Record tersimpan di tabel tugas
   ↓
[Notifikasi] → Kirim ke atasan
   ↓
[Atasan] → Review laporan tugas
   ↓
   ├─ Approve → Status = "disetujui"
   └─ Reject → Status = "ditolak"
   ↓
[Notifikasi] → Kirim hasil ke petugas
```

**Fitur Tambahan:**
- Kalender tugas (visualisasi tugas dalam kalender)
- Laporan tugas (rekap per periode)
- Print laporan tugas

---

### E. ALUR SANKSI

```
[Atasan] → Buka halaman sanksi
   ↓
[Atasan] → Input sanksi:
   - Pilih petugas
   - Jenis sanksi (SP1/SP2/SP3/dll)
   - Tanggal
   - Keterangan
   ↓
[SanksiController] → Validasi & simpan
   ↓
[Database] → Record tersimpan di tabel sanksi
   ↓
[Notifikasi] → Kirim ke petugas yang bersangkutan
   ↓
[Petugas] → Dapat melihat sanksi di halaman sanksi
```

**Akses Sanksi:**
- Atasan: Bisa input, edit, delete, dan print
- Petugas: Hanya bisa melihat sanksi sendiri
- Admin: Bisa melihat semua sanksi

---

### F. ALUR MANAJEMEN DATA (Admin)

```
[Admin] → Login ke sistem
   ↓
[Dashboard Admin] → Menu manajemen:
   ↓
   ├─ Users Management
   │   - CRUD users (tambah, edit, hapus)
   │   - Set role & tempat tugas
   │
   ├─ Tempat Tugas
   │   - CRUD lokasi/tempat tugas
   │
   ├─ Periode
   │   - CRUD periode absensi
   │   - Set periode aktif
   │
   ├─ Kalender Libur
   │   - Input tanggal libur nasional/cuti bersama
   │   - Hapus kalender
   │
   ├─ Buka Akses Absen
   │   - Buka akses absen untuk tanggal tertentu
   │   - (untuk kasus lupa absen)
   │
   ├─ Data Sensitif
   │   - Kelola data sensitif user
   │
   ├─ Pengaturan
   │   - Konfigurasi sistem
   │   - Jam kerja, toleransi keterlambatan, dll
   │
   └─ Activity Logs
       - Melihat log aktivitas sistem
       - Export logs
```

---

## 4. STRUKTUR DATABASE

### Tabel Utama:

#### **users**
- id_user (PK)
- nama
- username
- email
- password
- foto_profil
- id_role (FK)
- id_tempat (FK)

#### **absensi**
- id_absensi (PK)
- id_user (FK)
- id_periode (FK)
- tanggal
- jam_masuk, foto_masuk, latitude_masuk, longitude_masuk, lokasi_masuk
- jam_pulang, foto_pulang, latitude_pulang, longitude_pulang, lokasi_pulang
- status (hadir/terlambat/izin/sakit/alpha)
- keterangan

#### **cuti**
- id_cuti (PK)
- id_user (FK)
- id_pengganti (FK)
- id_periode (FK)
- tanggal_mulai, tanggal_selesai
- jenis_cuti
- alasan, alasan_lainnya
- alamat_cuti
- status (pending/disetujui/ditolak)
- approver_id (FK)

#### **tugas**
- id_tugas (PK)
- id_user (FK)
- id_periode (FK)
- tanggal_mulai, tanggal_selesai
- uraian
- status (pending/disetujui/ditolak)

#### **sanksi**
- id_sanksi (PK)
- id_user (FK)
- jenis_sanksi
- tanggal
- keterangan

#### **notifikasi**
- id_notifikasi (PK)
- id_user (FK)
- judul
- pesan
- is_read
- created_at

#### **roles**
- id_role (PK)
- nama_role

#### **tempat_tugas**
- id_tempat (PK)
- nama_tempat
- alamat
- latitude, longitude

#### **periode**
- id_periode (PK)
- nama_periode
- tanggal_mulai, tanggal_selesai
- is_active

#### **kalender**
- id_kalender (PK)
- tanggal
- keterangan (libur nasional/cuti bersama)

#### **pengaturan**
- id_pengaturan (PK)
- key
- value

#### **activity_logs**
- id (PK)
- user_id (FK)
- action
- description
- created_at

---

## 5. FITUR-FITUR SISTEM

### Fitur Umum (Semua Role):
- ✅ Login/Logout
- ✅ Dashboard dengan statistik
- ✅ Notifikasi real-time
- ✅ Profile management
- ✅ Ganti password
- ✅ Filter berdasarkan periode

### Fitur Petugas:
- ✅ Absen masuk/pulang dengan foto & GPS
- ✅ Riwayat absensi
- ✅ Print absensi
- ✅ Pengajuan cuti
- ✅ Print surat cuti
- ✅ Input laporan tugas
- ✅ Kalender tugas
- ✅ Laporan tugas
- ✅ Lihat sanksi

### Fitur Atasan:
- ✅ Lihat absensi bawahan
- ✅ Print absensi bawahan
- ✅ Approval cuti
- ✅ Approval tugas
- ✅ Input sanksi
- ✅ Print sanksi
- ✅ Kalender tugas tim

### Fitur Admin:
- ✅ CRUD Users
- ✅ CRUD Tempat Tugas
- ✅ CRUD Periode
- ✅ CRUD Kalender Libur
- ✅ Buka akses absen
- ✅ Kelola data sensitif
- ✅ Pengaturan sistem
- ✅ Activity logs
- ✅ Export logs

---

## 6. ALUR NOTIFIKASI

```
[Event Trigger] → Sistem mendeteksi event:
   ↓
   ├─ Absensi baru → Notif ke atasan
   ├─ Pengajuan cuti → Notif ke atasan
   ├─ Approval cuti → Notif ke petugas
   ├─ Laporan tugas → Notif ke atasan
   ├─ Approval tugas → Notif ke petugas
   └─ Sanksi baru → Notif ke petugas
   ↓
[NotifikasiController] → Buat record notifikasi
   ↓
[Database] → Simpan di tabel notifikasi
   ↓
[UI] → Badge notifikasi di navbar
   ↓
[User] → Klik notifikasi
   ↓
[Sistem] → Mark as read & redirect ke halaman terkait
```

---

## 7. KEAMANAN SISTEM

### Middleware & Guards:
1. **auth** - Memastikan user sudah login
2. **role:admin** - Hanya admin yang bisa akses
3. **role:atasan** - Hanya atasan yang bisa akses
4. **role:petugas** - Hanya petugas yang bisa akses

### Validasi Data:
- Input validation di setiap form
- CSRF protection
- SQL injection prevention (Eloquent ORM)
- XSS protection

### Data Sensitif:
- Password di-hash (bcrypt)
- Data sensitif terpisah di tabel user_sensitive
- Activity logging untuk audit trail

---

## 8. FLOW DIAGRAM LENGKAP

```
┌─────────────────────────────────────────────────────────────┐
│                      SISTEM ABSENSI                         │
└─────────────────────────────────────────────────────────────┘
                            |
                    ┌───────┴───────┐
                    │  AUTENTIKASI  │
                    └───────┬───────┘
                            |
            ┌───────────────┼───────────────┐
            │               │               │
      ┌─────▼─────┐   ┌────▼────┐   ┌─────▼─────┐
      │   ADMIN   │   │ ATASAN  │   │  PETUGAS  │
      └─────┬─────┘   └────┬────┘   └─────┬─────┘
            │              │              │
    ┌───────┴────────┐     │      ┌───────┴────────┐
    │                │     │      │                │
┌───▼───┐      ┌────▼──┐  │  ┌───▼────┐     ┌────▼────┐
│ Users │      │Tempat │  │  │Absensi │     │  Cuti   │
│  Mgmt │      │ Tugas │  │  │ Masuk/ │     │ Request │
└───────┘      └───────┘  │  │ Pulang │     └────┬────┘
                          │  └───┬────┘          │
┌────────┐    ┌────────┐  │      │               │
│Periode │    │Kalender│  │      │         ┌─────▼─────┐
│  Mgmt  │    │  Libur │  │      │         │ Approval  │
└────────┘    └────────┘  │      │         │   Cuti    │
                          │      │         └─────┬─────┘
┌─────────┐   ┌────────┐  │      │               │
│Settings │   │  Logs  │  │      │         ┌─────▼─────┐
└─────────┘   └────────┘  │      │         │Notifikasi │
                          │      │         └───────────┘
                          │      │
                    ┌─────▼──────▼─────┐
                    │   Laporan Tugas  │
                    │   & Approval     │
                    └──────────────────┘
                            │
                    ┌───────▼────────┐
                    │     SANKSI     │
                    │  (Input/View)  │
                    └────────────────┘
```

---

## 9. TEKNOLOGI & LIBRARY YANG DIGUNAKAN

### Backend:
- **Laravel 10.x** - PHP Framework
- **Eloquent ORM** - Database abstraction
- **Laravel Sanctum** - API authentication
- **Intervention Image** - Image processing (untuk foto absensi)

### Frontend:
- **Blade Template** - Laravel templating engine
- **Vite** - Asset bundling
- **Bootstrap/Tailwind** - CSS framework
- **JavaScript** - Interaktivitas
- **Geolocation API** - GPS tracking
- **Camera API** - Foto selfie

### Database:
- **MySQL/PostgreSQL** - Relational database

---

## 10. DEPLOYMENT & ENVIRONMENT

### Requirements:
- PHP >= 8.1
- Composer
- Node.js & NPM
- PostgreSQL
- Web Server (Apache/Nginx)

### Environment Variables (.env):
```
APP_NAME="Sistem Absensi"
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=http://localhost

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=absensi_db
DB_USERNAME=postgres
DB_PASSWORD=
```

### Docker Support:
- Dockerfile tersedia untuk containerization

---

## 11. KESIMPULAN

Sistem Absensi ini adalah aplikasi komprehensif yang mengelola:
1. **Kehadiran** - Absensi dengan foto & GPS
2. **Cuti** - Pengajuan dan approval
3. **Tugas** - Laporan dan monitoring
4. **Sanksi** - Manajemen disiplin
5. **Notifikasi** - Real-time updates
6. **Reporting** - Laporan dan print

Dengan 3 role utama (Admin, Atasan, Petugas) yang memiliki hak akses berbeda, sistem ini mendukung workflow approval berjenjang dan audit trail lengkap.
