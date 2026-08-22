import { execSync } from 'node:child_process';

/**
 * Provisions the E2E database, then wipes and re-migrates it so every run
 * starts from a known state. Uses the same env overrides as the web server.
 */
export default function globalSetup() {
    const env = { ...process.env, DB_DATABASE: 'octavia_e2e', QUEUE_CONNECTION: 'sync' };

    execSync('php scripts/create-e2e-db.php', { stdio: 'inherit', env });
    execSync('php artisan migrate:fresh --force', { stdio: 'inherit', env });
}
