import { expect, test } from '@playwright/test';

/**
 * Authenticated critical journey. Runs after auth.setup.ts (see
 * playwright.config.ts project dependencies) with its persisted session.
 * The UI is in German because onboarding chose Deutsch.
 */

test.use({ storageState: 'e2e/.auth/user.json' });

test.describe.serial('authenticated journey', () => {
    test('dashboard shows seeded stats in German', async ({ page }) => {
        await page.goto('/dashboard');
        await expect(page.getByRole('heading', { name: 'Dashboard' })).toBeVisible();
        await expect(page.locator('body')).toContainText(/prompts/i);
        await expect(page.locator('body')).toContainText(/benchmarks/i);
    });

    test('starter prompt exists in library', async ({ page }) => {
        await page.goto('/prompts');
        await expect(page.locator('a[href^="/prompts/"]:has(h2)')).toHaveCount(1);
        await expect(page.locator('h2', { hasText: 'Product tagline writer' })).toBeVisible();
    });

    test('optimize run completes and shows step-by-step detail', async ({ page }) => {
        await page.goto('/prompts');
        await page.locator('a[href^="/prompts/"]:has(h2)').first().click();
        await page.waitForURL(/\/prompts\/\d+$/);

        // pick the starter benchmark and launch optimization
        await page.selectOption('select', { index: 1 });
        await page.getByRole('button', { name: /Optimieren \(evolvieren\)/i }).click();

        await page.waitForURL(/\/runs\/\d+$/, { timeout: 60_000 });

        // sync queue completes immediately; assert terminal state + evidence
        await expect(page.locator('body')).toContainText('Abgeschlossen', { timeout: 30_000 });
        await expect(page.locator('body')).toContainText(/bester score/i);
        await expect(page.locator('body')).not.toContainText('Fehlgeschlagen');

        // the step timeline shows at least the initial evaluation
        await expect(page.locator('nav[aria-label="Run steps"] button').first()).toBeVisible();
    });

    test('playground returns model output without leaving the page', async ({ page }) => {
        await page.goto('/prompts');
        await page.locator('a[href^="/prompts/"]:has(h2)').first().click();
        await page.waitForURL(/\/prompts\/\d+$/);

        const playground = page.locator('textarea[placeholder]').last();
        await playground.fill('A solar-powered lamp, brand Solux');
        await page.getByRole('button', { name: /Playground/i }).click();

        await expect(
            page.locator('pre').filter({ hasText: /.+/ }).first(),
        ).toBeVisible({ timeout: 30_000 });
    });

    test('marketplace lists published item after publishing from prompt page', async ({ page }) => {
        await page.goto('/prompts');
        await page.locator('a[href^="/prompts/"]:has(h2)').first().click();
        await page.waitForURL(/\/prompts\/\d+$/);
        await page.getByRole('button', { name: 'Veröffentlichen' }).click();

        await page.goto('/marketplace');
        await expect(page.locator('h2', { hasText: 'Product tagline writer' })).toBeVisible();
    });
});

test.describe('public site', () => {
    test.use({ storageState: undefined });

    test('landing page explains the product and links to signup', async ({ page }) => {
        await page.goto('/');
        await expect(page).toHaveTitle(/Octavia/);
        await expect(page.getByRole('heading', { level: 1 })).toContainText('Benchmark your prompts');
        await expect(page.locator('#features')).toBeAttached();
    });
});
