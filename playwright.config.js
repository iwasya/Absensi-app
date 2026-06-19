// @ts-check
const { defineConfig, devices } = require('@playwright/test');

const baseURL = process.env.STAGING_BASE_URL;

module.exports = defineConfig({
  testDir: './tests/e2e',
  timeout: 60_000,
  expect: {
    timeout: 10_000,
  },
  use: {
    baseURL,
    trace: 'retain-on-failure',
    screenshot: 'only-on-failure',
    video: 'retain-on-failure',
    permissions: ['camera', 'geolocation'],
    geolocation: {
      latitude: Number(process.env.STAGING_TEST_LATITUDE || '-6.2030'),
      longitude: Number(process.env.STAGING_TEST_LONGITUDE || '106.8750'),
    },
    launchOptions: {
      args: [
        '--use-fake-ui-for-media-stream',
        '--use-fake-device-for-media-stream',
      ],
    },
  },
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],
});
