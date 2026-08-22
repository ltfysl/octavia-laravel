import { defineConfig } from '@playwright/test';

const PORT = 8021;
const baseURL = `http://127.0.0.1:${PORT}`;

// E2E runs against a dedicated MySQL database so it never touches dev data.
process.env.DB_DATABASE = 'octavia_e2e';
process.env.QUEUE_CONNECTION = 'sync';
export default defineConfig({
    testDir: './e2e',
    timeout: 90_000,
    expect: { timeout: 15_000 },
    fullyParallel: false,
    workers: 1,
    retries: 0,
    use: {
        baseURL,
        headless: true,
        screenshot: 'only-on-failure',
    },
    globalSetup: './e2e/global-setup.ts',
    projects: [
        { name: 'setup', testMatch: /auth\.setup\.ts/ },
        {
            name: 'journey',
            testMatch: /journey\.spec\.ts/,
            dependencies: ['setup'],
        },
    ],
    webServer: {
        command: `php -S 127.0.0.1:${PORT} -t public e2e/server-router.php`,
        url: `${baseURL}/up`,
        reuseExistingServer: false,
        timeout: 30_000,
    },
});

