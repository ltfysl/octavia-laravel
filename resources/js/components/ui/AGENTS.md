# AGENTS.md — resources/js/components/ui

## What lives here
Design-system components (`O*.vue`): OBadge.vue, OButton.vue, OEmptyState.vue, OField.vue, OInput.vue, OPanel.vue, ….

## Rules
- Prefix components with `O`; reuse before creating anything new.
- Props must be typed; emit events explicitly.
- No raw hex colors, ad-hoc shadows or generic card stacks; use `@theme` tokens.
- Keep components small, accessible and state-where-needed.
