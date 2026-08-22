<?php

namespace App\Notifications;

use App\Models\Run;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RunCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Run $run) {}

    /**
     * Mail + in-app feed. Mail respects the user's opt-out preference;
     * the database entry always lands so the bell stays accurate.
     */
    public function via(User $notifiable): array
    {
        return $notifiable->notify_run_completed_mail
            ? ['mail', 'database']
            : ['database'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        // Translate with the user's stored locale, not the app default.
        $locale = $notifiable->preferredLocale();
        $score = $this->run->best_score !== null ? round($this->run->best_score * 100).'%' : '—';

        return (new MailMessage)
            ->subject(__('notifications.run_completed.subject', ['name' => $this->run->name], $locale))
            ->markdown('mail.run-completed', [
                'run' => $this->run,
                'score' => $score,
                'locale' => $locale,
            ]);
    }

    public function toArray(User $notifiable): array
    {
        return [
            'run_id' => $this->run->id,
            'run_name' => $this->run->name,
            'status' => $this->run->status->value,
            'best_score' => $this->run->best_score,
        ];
    }
}
