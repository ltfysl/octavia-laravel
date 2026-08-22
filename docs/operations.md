# Operations

## Environment
| Concern | Dev | Production |
|---------|-----|------------|
| Queue | `QUEUE_CONNECTION=sync` (runs complete inline) | `database` + `php artisan queue:work` supervisor |
| Cache/session/file cache | database / array | redis or database |
| Mail | array/log | SMTP/transactional provider |
| LLM | mock | openai-compatible + key |

## Commands
```bash
php artisan serve                 # dev server
npm run dev                       # Vite dev server
npm run build                     # production assets
php artisan test                  # Pest suite
php vendor/bin/pint --dirty       # format changed files
php artisan wayfinder:generate    # regenerate TS routes after controller changes
```

## Scheduled tasks
None yet. Candidates for later: marketplace digest, stale-run reaper, benchmark snapshot refresh.

## Health & observability
- `GET /up` — framework health.
- `GET /api/health` — JSON status with app name + time.
- Failed queue jobs land in `failed_jobs`; inspect via `php artisan queue:failed`.
- Run-level failures are persisted on the run (`status`, `error`) and shown in the UI; unexpected exceptions additionally hit `storage/logs/laravel.log`.

## Backups
MySQL dumps of the `octavia` database. Prompts/benchmarks/runs are user data and must be included in any backup rotation. No external storage dependencies exist yet.

## Deployment checklist
1. `.env` production values (`APP_ENV=production`, `APP_DEBUG=false`, real URL, MySQL credentials, mail, LLM keys).
2. `composer install --no-dev --optimize-autoloader`.
3. `php artisan migrate --force`.
4. `npm ci && npm run build`.
5. `php artisan config:cache route:cache view:cache event:cache`.
6. Queue worker under supervisor (`queue:work --tries=1 --timeout=600`).
7. Verify `/up`, `/api/health`, sitemap.xml reachable.

## Known operational limits
- Evolution runs are single-process per run; no parallelization yet.
- No rate limiting on run creation beyond auth (add per-user quotas before public launch).
