<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent after onboarding completes — the first touch point that orients a
 * new user toward their first optimization run.
 */
class WelcomeNotification extends Notification
{
    use Queueable;

    public function __construct(protected User $user) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $locale = $notifiable->preferredLocale();

        return (new MailMessage)
            ->subject(__('notifications.welcome.subject', [], $locale))
            ->greeting(__('notifications.welcome.greeting', ['name' => $notifiable->name], $locale))
            ->line(__('notifications.welcome.line1', [], $locale))
            ->line(__('notifications.welcome.line2', [], $locale))
            ->action(__('notifications.welcome.cta'), url('/dashboard'))
            ->line(__('notifications.welcome.footer', [], $locale));
    }
}
