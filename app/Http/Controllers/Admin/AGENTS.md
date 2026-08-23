# AGENTS.md — app/Http/Controllers/Admin

## What lives here
Admin-only controllers: DashboardController.php, MarketplaceController.php, UserController.php.

## Rules
- Guarded by `EnsureUserIsAdmin` middleware; verify `auth.user.is_admin`.
- Render pages under `resources/js/pages/admin`.
- Keep admin business logic in dedicated services/actions; keep controllers thin.
- Reuse admin-specific policies and form requests.
