import { chromium } from 'playwright';

const base = 'http://127.0.0.1:8018';
const browser = await chromium.launch();
const page = await browser.newPage();
const errors = [];
page.on('pageerror', (e) => errors.push('pageerror: ' + e.message));
page.on('console', (m) => { if (m.type() === 'error') errors.push('console: ' + m.text()); });

// 1. Landing page
await page.goto(base + '/', { waitUntil: 'networkidle' });
console.log('landing title:', await page.title());

// 2. Register
await page.goto(base + '/register', { waitUntil: 'networkidle' });
await page.fill('#name', 'Smoke Tester');
await page.fill('#email', 'smoke@example.com');
await page.fill('#password', 'supersecret1');
await page.fill('#password_confirmation', 'supersecret1');
await page.click('button[type=submit]');
await page.waitForURL('**/welcome', { timeout: 10000 });
console.log('onboarding reached');

// 3. Complete onboarding with sample content
await page.click('text=Deutsch');
await page.click('button:has-text("Weiter")');
await page.waitForURL('**/dashboard', { timeout: 10000 });
console.log('dashboard reached');
const de = await page.textContent('h1');
console.log('dashboard h1 (de):', de);

// 4. Open starter prompt, start optimization run
await page.goto(base + '/prompts', { waitUntil: 'networkidle' });
await page.click('text=Product tagline writer');
await page.waitForURL('**/prompts/*', { timeout: 10000 });
await page.selectOption('select', { index: 1 });
await page.click('button:has-text("Optimieren (evolvieren)")');
await page.waitForURL('**/runs/*', { timeout: 10000 });
console.log('run page reached:', page.url());

// queue is sync? default QUEUE_CONNECTION=database -> need worker. Run job inline via artisan queue:work --once
console.log('waiting for run to complete...');
await browser.close();
console.log('errors:', JSON.stringify(errors, null, 2));
