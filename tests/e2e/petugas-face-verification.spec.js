const { test, expect } = require('@playwright/test');

const requiredEnv = [
  'STAGING_BASE_URL',
  'STAGING_PETUGAS_LOGIN',
  'STAGING_PETUGAS_PASSWORD',
];

test.beforeAll(() => {
  const missing = requiredEnv.filter((key) => !process.env[key]);
  if (missing.length) {
    throw new Error(`Missing staging e2e env: ${missing.join(', ')}`);
  }
});

test('petugas can open attendance page and start camera verification flow', async ({ page }) => {
  await page.goto('/login');

  await page.getByLabel(/Email atau Username/i).fill(process.env.STAGING_PETUGAS_LOGIN);
  await page.getByLabel(/^Password$/i).fill(process.env.STAGING_PETUGAS_PASSWORD);
  await page.getByRole('button', { name: /Masuk/i }).click();

  await expect(page).toHaveURL(/dashboard/);

  await page.goto('/petugas/absensi');
  await expect(page).toHaveTitle(/Absensi/i);

  const openCamera = page.locator('#btn_open_cam_masuk');
  if (await openCamera.count() === 0) {
    test.info().annotations.push({
      type: 'info',
      description: 'Form absen masuk tidak terbuka pada jadwal/status staging saat test dijalankan.',
    });
    return;
  }

  await expect(openCamera).toBeVisible();
  await openCamera.click();

  await expect(page.locator('#video_masuk')).toBeVisible();

  const capture = page.locator('#btn_capture_masuk');
  await expect(capture).toBeVisible();
  await capture.click();

  await expect(page.locator('#foto_masuk_input')).not.toHaveValue('');
  await expect(page.locator('#face_status_masuk')).toBeVisible();
});
