# Dashboard

## Job
Answer three questions in five seconds: **What do I have? What is running? What should I do next?**

## Composition (top to bottom)
1. **Stat row** — Prompts, Benchmarks, Active runs, Best avg score. Numbers only, no charts. Best score uses the OScoreBar color logic as text context.
2. **Quick actions** — four equal cards (create prompt / build benchmark / start run / browse marketplace). These are the activation paths; new accounts land here first.
3. **Recent runs** — last 6 runs with status badge, name, inline score bar, link to detail. Empty state routes to run creation, not documentation.

## Deliberate exclusions
- No activity feed yet (audit log exists in DB design but is not surfaced until multi-user value appears).
- No graphs beyond the run sparkline. Dashboards of vanity metrics are anti-value at this stage.
- Greeting by name was dropped in favor of the plain title — the dashboard is a tool surface, not a marketing moment.

## Data contract
`DashboardController` returns `stats` eagerly and `recentRuns` lazily (Inertia deferred prop) so the shell paints immediately.
