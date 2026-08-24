import { expect, test } from '@playwright/test';

/**
 * Setup: registers a fresh user, completes onboarding (German locale +
 * starter content) and persists the authenticated session for all
 * dependent journey tests.
 */

const unique = Date.now();
export const E2E_USER = {
    name: 'E2E Tester',
    email: `e2e-${unique}@example.com`,
};

test('authenticate and onboard', async ({ page }) => {
    await page.goto('/register');
    await page.fill('#name', E2E_USER.name);
    await page.fill('#email', E2E_USER.email);
    await page.fill('#password', 'supersecret1');
    await page.fill('#password_confirmation', 'supersecret1');
    await page.click('button[type=submit]');

    // Registration must land on onboarding
    await page.waitForURL('**/welcome');
    await expect(page.getByRole('heading', { name: /Welcome to your prompt lab/i })).toBeVisible();

    // Choose German + starter content
    await page.getByTestId('locale-de').click();
    await page.getByTestId('onboarding-next').click();
    await page.getByTestId('finish-onboarding').click();

    await page.waitForURL('**/dashboard');
    // WOW hero replaced the plain "Dashboard" h1 — assert on the hero text
    await expect(page.getByRole('heading', { name: /laboratory/i })).toBeVisible();

    // Persist the session for dependent tests
    await page.context().storageState({ path: 'e2e/.auth/user.json' });
});
