# AGENTS.md — app/Http/Controllers/Auth

## What lives here
Authentication flow controllers: AuthenticatedSessionController.php, EmailVerificationNotificationController.php, NewPasswordController.php, PasswordResetLinkController.php, RegisteredUserController.php, VerifyEmailController.php.

## Rules
- Use Laravel auth facilities; the UI is custom (`resources/js/pages/auth`).
- Send localized notifications for verification, reset and welcome emails.
- Keep controllers thin; validation in Form Requests.
- Respect email verification soft requirement (see root AGENTS.md).
