<?php

namespace App\Notifications;

use App\Models\MarketplaceReport;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MarketplaceReportResolvedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly MarketplaceReport $report) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'item_title' => $this->report->item?->title,
            'outcome' => $this->report->item?->published_at !== null ? 'kept' : 'unlisted',
        ];
    }
}
