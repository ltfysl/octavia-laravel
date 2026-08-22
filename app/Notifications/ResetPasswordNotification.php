<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Localized replacement for the framework ResetPassword mail — same i18n
 * pattern as VerifyEmailNotification: the framework version pre-translates
 * its lines before any locale switch, so we rebuild them eagerly with the
 * recipient's stored locale.
 */
class ResetPasswordNotification extends ResetPassword
{
    public function __construct(protected User $user, $token)
    {
        parent::__construct($token);
    }

    protected function resetUrl($notifiable): string
    {
        if (static::$createUrlCallback) {
            return call_user_func(static::$createUrlCallback, $notifiable, $this->token);
        }

        return url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }

    protected function buildMailMessage($url): MailMessage
    {
        $locale = $this->user->preferredLocale();

        return (new MailMessage)
            ->subject(__('auth.reset.subject', [], $locale))
            ->greeting(__('auth.verify.greeting', ['name' => $this->user->name], $locale))
            ->line(__('auth.reset.line1', [], $locale))
            ->action(__('auth.reset.action', [], $locale), $url)
            ->line(__('auth.reset.expire', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')], $locale))
            ->line(__('auth.reset.line2', [], $locale));
    }
}
