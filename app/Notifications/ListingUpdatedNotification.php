<?php

namespace App\Notifications;

use App\Models\MarketplaceItem;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Sent to every user with a tracked install when the publisher bumps a
 * listing to a new version (republish). Database channel only — the bell
 * badge and the marketplace card surface the update.
 */
class ListingUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly MarketplaceItem $item, private readonly int $newVersion) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'item_title' => $this->item->title,
            'new_version' => $this->newVersion,
        ];
    }
}
