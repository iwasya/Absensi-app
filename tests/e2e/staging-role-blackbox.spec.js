const { test, expect } = require('@playwright/test');

const baseURL = process.env.STAGING_BASE_URL;

const roles = [
  {
    name: 'petugas',
    login: process.env.STAGING_PETUGAS_LOGIN,
    password: process.env.STAGING_PETUGAS_PASSWORD,
    allowed: ['/dashboard', '/petugas/absensi', '/petugas/tugas'],
    forbidden: ['/admin/users', '/atasan/absensi'],
  },
  {
    name: 'atasan',
    login: process.env.STAGING_ATASAN_LOGIN,
    password: process.env.STAGING_ATASAN_PASSWORD,
    allowed: ['/dashboard', '/atasan/absensi', '/atasan/tugas'],
    forbidden: ['/admin/users', '/petugas/absensi'],
  },
  {
    name: 'admin',
    login: process.env.STAGING_ADMIN_LOGIN,
    password: process.env.STAGING_ADMIN_PASSWORD,
    allowed: ['/dashboard', '/admin/users', '/admin/pengaturan'],
    forbidden: ['/petugas/absensi', '/atasan/absensi'],
  },
];

test.beforeAll(() => {
  const missing = [
    'STAGING_BASE_URL',
    'STAGING_PETUGAS_LOGIN',
    'STAGING_PETUGAS_PASSWORD',
    'STAGING_ATASAN_LOGIN',
    'STAGING_ATASAN_PASSWORD',
    'STAGING_ADMIN_LOGIN',
    'STAGING_ADMIN_PASSWORD',
  ].filter((key) => !process.env[key]);

  if (missing.length) {
    throw new Error(`Missing staging e2e env: ${missing.join(', ')}`);
  }
});

test('unauthenticated users are redirected to login', async ({ request }) => {
  for (const path of ['/', '/dashboard', '/admin/users', '/petugas/absensi', '/atasan/absensi']) {
    const response = await request.get(path, { maxRedirects: 0 });
    expect([302, 303]).toContain(response.status());
    expect(response.headers().location || '').toContain('/login');
  }
});

test('invalid login stays on login page with an error', async ({ page }) => {
  await page.goto('/login');
  await page.getByLabel(/Email atau Username/i).fill('invalid@example.test');
  await page.getByLabel(/^Password$/i).fill('WrongPassword123');
  await page.getByRole('button', { name: /Masuk/i }).click();

  await expect(page).toHaveURL(/\/login$/);
  await expect(page.locator('body')).toContainText(/tidak valid|salah|gagal/i);
});

for (const role of roles) {
  test(`${role.name} can login and access only permitted pages`, async ({ page }) => {
    await page.goto('/login');
    await page.getByLabel(/Email atau Username/i).fill(role.login);
    await page.getByLabel(/^Password$/i).fill(role.password);
    await page.getByRole('button', { name: /Masuk/i }).click();

    await expect(page).not.toHaveURL(/\/login$/);
    await expect(page).toHaveURL(/\/dashboard/);

    for (const path of role.allowed) {
      const response = await page.goto(baseURL + path);
      expect(response.status(), `${role.name} allowed ${path}`).toBe(200);
    }

    for (const path of role.forbidden) {
      const response = await page.goto(baseURL + path);
      expect(response.status(), `${role.name} forbidden ${path}`).toBe(403);
    }
  });
}
