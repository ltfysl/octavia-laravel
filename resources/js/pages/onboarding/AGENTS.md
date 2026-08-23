# AGENTS.md — resources/js/pages/onboarding

## What lives here
Inertia pages for the `onboarding` domain: Welcome.vue.

## Rules
- File name → controller dot-path; keep the mapping exact.
- Build the page out of existing `O*` design-system components; avoid new one-off UI.
- Use typed props from Inertia and `vue-i18n` for all labels.
- No business logic here; call actions/endpoints for mutations.
