# Feature gap vs. octavia-old (Next.js reference)

> Reference: `/Users/latifyesil/Projekte/octavia-old/` (no `Downloads/test` available, per runtime check).  
> This doc tracks which features from the older Next.js app are already present, partially present, or missing in the current Laravel app.

## Present in current Laravel app

- Dashboard with metrics + recent runs
- Prompts CRUD + versions + diff/restore
- Benchmarks CRUD + cases/criteria + wizard + AI generation
- Evolution/evaluation engine (evaluate + optimize runs)
- Runs index/detail with live progress, steps, diagnosis
- Collections
- Marketplace publish/install/browse/report
- Teams (workspaces) + invites
- Tournaments
- Notifications + mark-read/delete
- Search across prompts/benchmarks/runs
- Reports / Insights
- Audit log page + API
- Settings (profile, password, sessions, billing placeholder, **config presets**)
- Dark mode + command palette + O* design system
- Assistant chat + prompt/benchmark AI insight + run diagnosis + report recommendations

## Partially present / needs polish

- Dashboard: **score trend chart**, **leaderboard**, **category breakdown**, and **score distribution** ✓; sparkline still basic
- Activity timeline: global feed exists at /activity and /api/v1/activity ✓
- Settings: **Provider Keys** ✓; no Workspace; **API key management + usage tracking** ✓
- Export: prompt index CSV; **leaderboard CSV export with benchmark/run filters** ✓; dashboard export link ✓
- Playground: **standalone chat playground at /playground** ✓; prompt detail now has **multi-turn chat playground** ✓
- Version diff: works; **AI diff explanation** ✓
- Prompt analytics: per-prompt runs, average/best score, score-over-time sparkline, per-benchmark breakdown — basic version on prompt detail

## Missing (candidate backlog)

- **A/B testing for prompts** — compare two prompt versions against a benchmark ✓ (basic UI + EvaluateService backend)
- **Regression testing** — verify a new version does not break existing cases
- **Prompt/skill analytics** — per-prompt run stats, score over time
- **Skill templates** — reusable starting prompt templates ✓
- **Gallery star/fork** — star count, fork count in marketplace ✓
- **Webhooks + deliveries** — outbound webhooks for run events ✓
- **API keys + usage tracking** — create/revoke scopes, per-key call logs ✓
- **Multi-model tournament** — run same prompt/benchmark across several providers ✓
- **Cost optimizer** — cheaper model for generation, stronger for evaluation
- **Onboarding wizard** — multi-step first-run flow beyond current welcome page
- **Real-time WebSocket progress** — currently polling (Reverb wired but not universal)
