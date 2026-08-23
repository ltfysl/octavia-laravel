# AGENTS.md — routes

## What lives here
Laravel route definitions: api.php, channels.php, console.php, verification.php, web.php.

## Rules
- Use named routes and route groups; prefer `route()` for URL generation.
- Apply middleware (auth, verified, abilities, admin) explicitly.
- Keep each route file focused: web, api, channels, console, verification.
