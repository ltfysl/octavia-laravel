# AGENTS.md — app/Enums

## What lives here
All categorical domain enums: BenchmarkCategory.php, CriterionType.php, MarketplaceItemType.php, RunMode.php, RunStatus.php, StepPhase.php, ….

## Rules
- Never use raw strings for statuses, types or categories.
- Back enums with strings for storage.
- New case + migration review.
