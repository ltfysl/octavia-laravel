# AGENTS.md — resources/js/pages

## What lives here
Inertia page components, one per rendered dot-path.

## Rules
- Page file path must match the dot-path the controller returns.
- Compose pages from `resources/js/components/ui/O*.vue`; never use raw hex colors or ad-hoc shadows.
- Receive data through Inertia props; no direct database calls from the page.
- Use `vue-i18n` for user-facing strings; no hard-coded copy.
- Prefer Wayfinder-generated route/helpers when navigating or calling endpoints.
