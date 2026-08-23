# AGENTS.md — resources/js/layouts

## What lives here
Page layout shells: AppLayout.vue, AuthLayout.vue, PublicLayout.vue.

## Rules
- Layouts wrap pages via Inertia; no business logic here.
- Use slots for page content; keep responsive and accessible.
- Public, Auth and App layouts are the three canonical shells.
