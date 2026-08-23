# AGENTS.md — resources/js/pages/prompts

## What lives here
Inertia pages for the `prompts` domain: Create.vue, Index.vue, Show.vue.

## Rules
- File name → controller dot-path; keep the mapping exact.
- Build the page out of existing `O*` design-system components; avoid new one-off UI.
- Use typed props from Inertia and `vue-i18n` for all labels.
- No business logic here; call actions/endpoints for mutations.
