<div align="center">

# Octavia

**The prompt laboratory. Benchmark, evolve and fine-tune prompts — not models.**

</div>

---

## What is Octavia?

Most people generate prompts by gut feeling and reuse them in contexts they were never tested for. Octavia applies scientific method to prompt engineering:

- **Benchmarks** — build test suites with concrete success criteria (required phrases, forbidden content, regex patterns, AI-judged requirements) for any domain: coding, marketing, sales, support.
- **Evolution engine** — start from any prompt and let Octavia evaluate → mutate → select in a loop until your target score is reached. Every step is persisted: the exact prompt, the score, which requirements failed and why.
- **Versioned library** — every change is a version with a changelog; restore anything, always know which prompt produced which result.
- **Marketplace** — install benchmarks and prompts from the community (versioned), publish your own.

*Like model fine-tuning — except you tune the prompt.*

## Tech stack

| Layer | Choice |
|-------|--------|
| Backend | Laravel 13 · PHP 8.4 · MySQL 8 |
| Frontend | Inertia v2 · Vue 3 · TypeScript · Tailwind CSS v4 |
| Routing | Laravel Wayfinder (typed TS routes) |
| LLMs | Provider abstraction: deterministic mock (no key needed) + any OpenAI-compatible endpoint |
| Testing | Pest 4 |
| i18n | English + German, first-class on backend and frontend |

## Quick start

**Requirements:** PHP 8.4+, Composer, Node 22+, MySQL 8.

```bash
git clone <repo> octavia && cd octavia
composer install
npm install

# Environment
cp .env.example .env          # defaults expect MySQL root/12345678 on 127.0.0.1
php artisan key:generate

# Database (create `octavia` first if needed)
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS octavia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
php artisan migrate --seed     # --seed optional demo data

# Frontend
npm run dev                    # or: npm run build
```

```bash
php artisan serve              # http://127.0.0.1:8000
```

Register an account, accept the starter content during onboarding, open the sample prompt and hit **Optimize** — you'll watch a full evolution run complete without any API key.

### Using a real model

```env
OCTAVIA_LLM_PROVIDER=openai
OPENAI_API_KEY=sk-...
# OPENAI_BASE_URL=https://api.openai.com/v1   # or OpenRouter/Ollama/Azure
# OPENAI_MODEL=gpt-4o-mini
```

## Development commands

```bash
php artisan test               # Pest test suite
npx vue-tsc --noEmit           # frontend type check
npx playwright test            # browser E2E suite (dedicated octavia_e2e DB)
npm run build                  # production assets
php artisan wayfinder:generate # regenerate typed routes
```

## Architecture at a glance

```
app/
├── Actions/        # focused use cases (e.g., CreateStarterContent)
├── Enums/          # BenchmarkCategory, CriterionType, RunStatus, ...
├── Http/           # thin controllers + Form Requests + middleware
├── Jobs/           # ProcessRunJob — single entry point for runs
├── Models/         # Prompt(s), Benchmark(s+cases+criteria), Run(s+steps+results)
├── Policies/       # Prompt/Benchmark/Run authorization
└── Services/
    ├── EvaluationService.php   # pure scoring core
    ├── EvolutionService.php    # hill-climbing optimizer
    └── Llm/                    # provider contract, mock & OpenAI-compatible drivers
```

The evaluation engine scores criteria locally where possible (`contains`, `regex`) and uses the configured provider as a strict judge for semantic criteria. Full engine docs: [`docs/features/engine.md`](docs/features/engine.md). Architecture decisions: [`docs/decisions.md`](docs/decisions.md).

## Public API (v1)

Token-authenticated via Sanctum. Tokens accept **fine-grained scopes** —
`prompts:read`, `prompts:write`, `runs:read`, `runs:write` — or the legacy
coarse abilities (`read`, `write`; `write` implies read, `res:write` implies
`res:read`). Unknown abilities are rejected at issuance:

```bash
# obtain a scoped token
curl -X POST http://localhost:8000/api/v1/auth/token \
  -H "Content-Type: application/json" \
  -d '{"email":"you@example.com","password":"...","device_name":"cli","abilities":["runs:read","runs:write"]}'

# use it
curl -H "Authorization: Bearer <token>" http://localhost:8000/api/v1/prompts
```


| Endpoint | Description |
|----------|-------------|
| `POST /api/v1/auth/token` | Issue token (rate limited 6/min) |
| `DELETE /api/v1/auth/token` | Revoke current token |
| `GET /api/v1/me` | Current user |
| `GET/POST /api/v1/prompts` · `GET /api/v1/prompts/{id}` | Prompt library |
| `POST /api/v1/prompts/{id}/evaluate` | Synchronous single-pass evaluation against a benchmark |
| `GET/POST /api/v1/runs` · `GET /api/v1/runs/{id}` · `POST /api/v1/runs/{id}/cancel` | Runs |

## Documentation

| Doc | Content |
|-----|---------|
| [AGENTS.md](AGENTS.md) | Operating manual for coding agents & new engineers |
| [DESIGNS.md](DESIGNS.md) | Design system overview → `designs/*` specs |
| [docs/architecture.md](docs/architecture.md) | Layers, domain model, data flow |
| [docs/features/engine.md](docs/features/engine.md) | Evolution engine semantics |
| [docs/operations.md](docs/operations.md) | Environments, queue, deploy checklist |
| [tasks.md](tasks.md) | Active development backlog |

## Production notes

- Set `QUEUE_CONNECTION=database` and run a supervisor-managed `php artisan queue:work`.
- Cache config/routes/views; verify `/up` and `/api/health`.
- `robots.txt` ships with the app; `/sitemap.xml` lists all public pages.
- No third-party accounts are required to run the product. The only optional external dependency is an LLM API key.

## Troubleshooting

| Symptom | Fix |
|---------|-----|
| Runs stay "pending" forever | Queue is `database` but no worker running: `php artisan queue:work` — or set `QUEUE_CONNECTION=sync` for local dev |
| "Unknown LLM provider" on run start | Provider config missing key — see config/llm.php and .env example |
| Wayfinder imports missing after adding controllers | `php artisan wayfinder:generate` |
| Tests fail with DB errors | Ensure MySQL credentials match `.env`; suite uses sqlite in-memory by default via phpunit.xml |
