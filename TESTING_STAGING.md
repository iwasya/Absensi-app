# Testing Staging

Dokumen ini menjelaskan cara menjalankan pengujian otomatis tanpa menyentuh data produksi.

## 1. Environment staging

Siapkan aplikasi staging terpisah dari produksi:

- Gunakan domain berbeda, misalnya `https://staging.example.test`.
- Gunakan database berbeda, misalnya `absensi_staging`.
- Gunakan file `.env` staging berdasarkan `.env.staging.example`.
- Jangan gunakan akun, database, atau storage produksi untuk pengujian create/update/delete.

Setelah deploy staging:

```bash
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 2. Feature test Laravel

Feature test Laravel memakai SQLite in-memory dari `phpunit.xml`, sehingga tidak menyentuh database lokal atau produksi.

```bash
php artisan test
```

## 3. Browser/e2e test Playwright

Install browser Playwright satu kali di mesin test:

```bash
npx playwright install chromium
```

Export credential staging. Gunakan akun khusus staging, bukan akun produksi.

```bash
export STAGING_BASE_URL="https://staging.example.test"
export STAGING_PETUGAS_LOGIN="petugas-staging@example.test"
export STAGING_PETUGAS_PASSWORD="password-staging"
export STAGING_TEST_LATITUDE="-6.2030"
export STAGING_TEST_LONGITUDE="106.8750"
```

Jalankan test:

```bash
npm run test:e2e
```

Test e2e saat ini bersifat non-destruktif: login petugas, membuka halaman absensi, membuka kamera palsu browser, mengambil foto palsu, dan memastikan status verifikasi wajah muncul. Alur create/update/delete dapat ditambahkan di staging setelah data dummy tersedia.
