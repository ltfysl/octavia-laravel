# AGENTS.md — app/Http/Controllers

## What lives here
Web-facing HTTP controllers: AuditLogController.php, BenchmarkController.php, BenchmarkExportController.php, BenchmarkImportController.php, CollectionController.php, Controller.php, ….

## Rules
- Controllers stay thin: validate (Form Request), authorize (Policy), delegate, render.
- Render Inertia dot-paths that match `resources/js/pages/**`.
- Use method injection; never execute DB queries directly.
- For non-CRUD endpoints prefer single-action invokable controllers.
