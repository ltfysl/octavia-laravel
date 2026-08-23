# AGENTS.md — app/Notifications

## What lives here
User notifications: ListingUpdatedNotification.php, MarketplaceReportResolvedNotification.php, ResetPasswordNotification.php, RunCompletedNotification.php, TeamInvitationNotification.php, VerifyEmailNotification.php, ….

## Rules
- Localize via `User::preferredLocale()` and explicit `$locale`.
- Mail templates live in `resources/views/mail`.
- Never expose sensitive data.
