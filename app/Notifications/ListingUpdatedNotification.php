<?php

namespace App\Notifications;

use App\Models\MarketplaceItem;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
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
        return $notifiable->notify_listing_updates_mail ? ['mail', 'database'] : ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $locale = $notifiable->preferredLocale();

        return (new MailMessage)
            ->subject(__('notifications.listingUpdated.subject', ['title' => $this->item->title], $locale))
            ->line(__('notifications.listingUpdated.line1', ['title' => $this->item->title, 'version' => $this->newVersion], $locale))
            ->action(__('notifications.listingUpdated.cta'), url('/marketplace'))
            ->line(__('notifications.listingUpdated.footer', [], $locale));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'item_title' => $this->item->title,
            'new_version' => $this->newVersion,
        ];
    }
}
