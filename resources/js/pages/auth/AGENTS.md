# AGENTS.md — resources/js/pages/auth

## What lives here
Inertia pages for the `auth` domain: ForgotPassword.vue, Login.vue, Register.vue, ResetPassword.vue.

## Rules
- File name → controller dot-path; keep the mapping exact.
- Build the page out of existing `O*` design-system components; avoid new one-off UI.
- Use typed props from Inertia and `vue-i18n` for all labels.
- No business logic here; call actions/endpoints for mutations.
