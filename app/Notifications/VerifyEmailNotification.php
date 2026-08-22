<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

/**
 * Localized replacement for the framework VerifyEmail mail.
 * The framework version pre-translates its lines before the locale
 * switch happens, so DE users received English content. This subclass
 * builds every line with the recipient's stored locale.
 */
class VerifyEmailNotification extends VerifyEmail
{
    public function __construct(protected User $user) {}

    protected function verificationUrl($notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes((int) config('auth.verification.expire', 60)),
            ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->email)],
        );
    }

    protected function buildMailMessage($url): MailMessage
    {
        $locale = $this->user->preferredLocale();

        return (new MailMessage)
            ->subject(__('auth.verify.subject', [], $locale))
            ->greeting(__('auth.verify.greeting', ['name' => $this->user->name], $locale))
            ->line(__('auth.verify.line1', [], $locale))
            ->action(__('auth.verify.action', [], $locale), $url)
            ->line(__('auth.verify.thanks', [], $locale));
    }
}
