# AGENTS.md — app/Providers

## What lives here
Service providers: AppServiceProvider.php.

## Rules
- Boot methods should only register services.
- Keep binding registration explicit.
- Centralize quotas/abilities/config here.
