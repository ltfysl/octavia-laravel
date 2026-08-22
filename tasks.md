# Tasks

## Now
- [ ] Billing (credits model fits usage-based LLM costs; Stripe integration)

## Next
- [ ] Finer-grained token scopes (per-resource) with billing tiers
- [ ] Welcome mail polish (optional)
- [ ] Team invitation emails

## Later
- [ ] Export/import prompts as JSON files — done
- [ ] Search across runs — done
- [ ] "Update available" notification when publisher republishes at new version (email channel)

## Human Action Required
- [ ] Provide an OpenAI-compatible API key to test real-model runs (`OPENAI_API_KEY` in `.env`)
- [ ] Production domain, DNS, TLS certificate
- [ ] Stripe account if/when billing launches
- [ ] Transactional email provider credentials for production mail

## Completed
- [x] Laravel 13 scaffold, Inertia v2 + Vue 3 + TS, Tailwind v4 tokens, Wayfinder
- [x] Domain schema: prompts(+versions), benchmarks(+cases+criteria), collections, runs(+steps+case+criteria results), marketplace items
- [x] Models, factories, policies
- [x] LLM provider abstraction (mock with judge/optimizer modes + OpenAI-compatible driver)
- [x] Evaluation engine (contains/not_contains/regex/llm_judge, weighted scoring)
- [x] Evolution engine (hill-climbing, stale detection, step persistence, version promotion)
- [x] Custom auth (register/login/logout/forgot/reset), onboarding with starter content
- [x] App shell + design system components (OButton/OPanel/OInput/OField/OBadge/OScoreBar/OEmptyState)
- [x] Dashboard, prompts CRUD+versions, benchmark wizard+detail, runs create/index/detail/timeline, marketplace browse/install/publish, settings (profile/password/locale/sessions)
- [x] i18n EN/DE across app pages; locale persistence per user + cookie
- [x] Landing page + legal pages + SEO (titles, meta, OG, JSON-LD, robots.txt, sitemap.xml)
- [x] Marketplace publish buttons on prompt/benchmark detail pages; full publish→install loop verified in browser
- [x] Live run-progress polling (2s interval, partial reload on terminal status)
- [x] Prompt playground: ad-hoc evaluation of current or unsaved prompt content (`POST /prompts/{id}/playground` + UI)
- [x] Benchmark collections: create/manage UI, owner-scoped membership validation, whole-collection runs
- [x] Sanctum API v1: token auth with read/write abilities, prompts CRUD-read + sync evaluate, runs start/show/cancel with ownership checks and rate limits
- [x] Admin panel: platform stats, newest users, latest runs, user search/promote/demote/delete with self-protection
- [x] Round 3: word-level prompt version diff (ODiff + LCS util), localized run-completion mail, auth rate limiter, security headers middleware, landing polish
- [x] Round 5: per-notification delete, diff API endpoint, full email verification flow
- [x] Round 6: localized VerifyEmailNotification, per-user daily run quota
- [x] Round 7: localized ResetPasswordNotification, run-quota shared prop + amber soft warning banner
- [x] Round 8: Sanctum token abilities + admin marketplace moderation
- [x] Round 9: marketplace abuse reports + admin reports queue
- [x] Round 10: notifications index page (pagination, mark-unread, delete), reporter resolution notifications
- [x] Round 11: benchmark snapshot versioning on publish + localized welcome mail after onboarding
- [x] Round 12: marketplace install tracking + update-available badge and Update button
- [x] Round 13: republish notifications for installed users
- [x] Round 14: team workspaces (Team + TeamMember models with roles, CRUD + invite/remove UI, team-aware visibility scope)
- [x] Round 15: benchmark JSON export/import, global search across prompts/benchmarks, favicon.svg
- [x] Round 16: branded error pages (403/404/419/429/500/503) + republish notification tests
- [x] Round 17: prompt JSON export/import, global search includes runs, error page tests
- [x] Round 18: reporter resolution email (queueable, localized, opt-out aware), run-level team scoping (policy + scope + index owner attribution)
- [x] **108 Pest tests / 378 assertions green**, Playwright E2E 7 passed, Pint clean, vue-tsc clean, build green
- [x] Docs: README, AGENTS.md, DESIGNS.md + designs/*, docs/architecture, docs/features/engine, docs/operations, docs/decisions
- [x] Rounds 1–18 documented in rounds/
