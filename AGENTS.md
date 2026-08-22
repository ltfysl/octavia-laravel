# AGENTS.md — Octavia

Operating manual for autonomous coding agents working on this repository. Read this before changing anything.

## Product
**Octavia** is a prompt laboratory: users define benchmarks (test cases with success criteria), then run evaluation or evolution (hill-climbing prompt optimization) against them, inspect every step, keep winning prompts as new versions, and share via a versioned marketplace. Core promise: *fine-tuning, but for prompts.*

## Stack
- Laravel 13, PHP 8.4, MySQL 8 (`octavia` DB, root/12345678 locally)
- Inertia v2 + Vue 3 + TypeScript, Tailwind CSS v4 (`@theme` tokens in `resources/css/app.css`)
- Laravel Wayfinder (TS route generation), Vite 8
- Pest 4 for all tests; Pint for PHP formatting
- vue-i18n (frontend EN/DE) + Laravel lang files (backend)

## Commands
```bash
php artisan test            # full suite — must pass before yielding
php vendor/bin/pint --dirty # format touched files
npm run build               # must succeed before yielding
npx vue-tsc --noEmit        # frontend types — must pass
npx playwright test         # browser E2E (dedicated octavia_e2e DB, wiped per run)
php artisan wayfinder:generate --skip-actions --skip-routes  # after controller changes (optional)
```

**E2E isolation**: the Playwright suite runs against `octavia_e2e` via `e2e/server-router.php`, which force-sets `DB_DATABASE` before boot. Never point E2E at the dev database; if you change the server bootstrap, verify with a DB row diff afterwards.
**Email verification** is a soft requirement: routes in `routes/verification.php`, mail sent at registration via the Registered event, amber banner until verified. `User implements MustVerifyEmail`.


**Version ordering pitfall**: `Prompt::versions()` orders newest-first. Code assuming chronological order must re-sort explicitly (see `Api/PromptController::diff`).


**Run quota**: run creation (web + API) is wrapped in `throttle:runs` — `Limit::perDay(config('llm.run_quota_per_day'))` keyed by user id, defined in AppServiceProvider. Raise/lower via `OCTAVIA_RUN_QUOTA_PER_DAY`.


**Token abilities**: API v1 tokens carry a closed ability set (`read`, `write`) validated at issuance (`AuthTokenController`) and enforced via `abilities:` middleware in `routes/api.php`. Write endpoints require `write`; evaluate requires `read`. Default issuance is `['read']` only. Register any new ability names in the issuance validation rule.

## Directory map
- `app/Services/EvaluationService.php`, `app/Services/EvolutionService.php` — engine core. **Do not add DB writes to EvaluationService.**
- `app/Services/Llm/` — provider contract + mock + openai-compatible + manager.
- `app/Jobs/ProcessRunJob.php` — the only entry point that executes runs.
- `app/Enums/` — all enum values live here; never scatter magic strings.
- `resources/js/components/ui/O*.vue` — design system. Reuse before creating anything new.
- `resources/js/pages/**` — Inertia pages; controllers render these exact dot-paths.
- `designs/` + `DESIGNS.md` — design system docs; follow them for any UI work.
- `routes/api.php` + `app/Http/Controllers/Api/` — Sanctum-protected API v1; JSON shapes in `app/Http/Resources/`. Ownership is enforced inline (`abort_unless($x->user_id === auth id, 404)`).
- `app/Http/Controllers/Admin/` + `resources/js/pages/admin/` — admin-only (`EnsureUserIsAdmin` middleware); admin link renders only when `auth.user.is_admin`.
- `resources/js/directives/reveal.ts` (`v-reveal`) — the only sanctioned scroll motion. Never hide content under `prefers-reduced-motion`.
- `app/Notifications/` — localized via `User::preferredLocale()`; translate inside `toMail` with explicit `$locale` (the app locale at build time is NOT the user's).
- `app/Actions/RunPlayground.php` — ad-hoc evaluation, no persistence. The `LlmProvider` interface is container-bound to the configured default.
- `app/Http/Middleware/HandleInertiaRequests.php` — shares the `notifications` prop (unread + last 6) the app-shell bell renders; mark-read lives at `POST /notifications/mark-read`.

## Conventions
### Backend

- Controllers stay thin: validate (Form Requests where reusable), authorize (Policies), delegate to services/actions, render.
- Domain logic goes in `app/Services/` or `app/Actions/`; models hold relations, scopes and casts only.
- Enums for everything categorical. New statuses/modes = new enum case + migration review.
- Queue anything slower than ~1s in production paths; jobs fail gracefully onto the owning record (see ProcessRunJob).
- Tests use factories; RefreshDatabase is bound globally in `tests/Pest.php`.
- The mock LLM provider has a documented behavior contract (see its docblock). Changing it breaks tests by design — update both together.

### Frontend
- Pages are composed of `O*` components; raw hex colors or ad-hoc shadows are bugs — use theme tokens.
- Prefer explicit TS types; `vue-tsc` clean is part of done.
- Wayfinder routes exist but pages currently use plain hrefs for links rendered inside lists; prefer Wayfinder functions when calling endpoints from JS.

### Localization
- Backend strings go through `__()` with keys in `lang/en/*.php` + `lang/de/*.php`.
- Locale resolution: user column → cookie `octavia_locale` → app default (SetLocale middleware).

### Database
- Migrations are paired create/down; FKs cascade where children belong to parents.
- Result rows snapshot labels (e.g., `criterion_label`) so history survives source edits.
- Indexes on every FK plus hot query paths (user_id+created_at patterns).

## Definition of done (per change)
1. Behavior implemented end to end (UI → controller → service).
2. Authorization checked via policies.
3. Validation on write paths.
4. `php artisan test` green; targeted tests added for new behavior.
5. `pint` clean, `npm run build` + `vue-tsc` clean for frontend changes.
6. Docs updated if architecture/product surface changed (AGENTS.md, docs/, designs/, README).
7. `tasks.md` updated; completed items checked off.

## Areas requiring special care
- **EvolutionService loop**: best-tracking order matters (update best BEFORE target check); stale counter drives termination. Don't "simplify" without re-running `EvolutionEngineTest`.
- **Marketplace install**: deep-copies prompts/benchmarks between users inside a transaction — never link shared state across users.
- **Prompt versions**: `current_version_id` must always be updated transactionally with version creation.
- **MockLlmProvider**: its echo/judge/optimize heuristics ARE the test oracle.

## Known gaps / next candidates
See `tasks.md`. Highlights: Sanctum API tokens, real-time run progress polling wiring, admin panel, Stripe billing, email verification enforcement, Playwright E2E suite.
