<?php

namespace App\Notifications;

use App\Models\MarketplaceReport;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MarketplaceReportResolvedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly MarketplaceReport $report) {}

    /**
     * Mail + in-app feed. Mail respects the user's opt-out preference
     * (reuses the shared notify_*_mail pattern from RunCompletedNotification);
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
        $locale = $notifiable->preferredLocale();
        $kept = $this->report->item?->published_at !== null;

        return (new MailMessage)
            ->subject(__('notifications.report_resolved.subject', [], $locale))
            ->greeting(__('notifications.report_resolved.greeting', ['name' => $notifiable->name], $locale))
            ->line(__('notifications.report_resolved.intro', ['title' => $this->report->item?->title], $locale))
            ->line($kept
                ? __('notifications.report_resolved.kept', [], $locale)
                : __('notifications.report_resolved.unlisted', [], $locale))
            ->action(__('notifications.report_resolved.action'), url('/marketplace'));
    }

    public function toArray(User $notifiable): array
    {
        return [
            'item_title' => $this->report->item?->title,
            'outcome' => $this->report->item?->published_at !== null ? 'kept' : 'unlisted',
        ];
    }
}
