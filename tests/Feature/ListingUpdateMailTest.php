<?php

use App\Models\MarketplaceItem;
use App\Models\User;
use App\Notifications\ListingUpdatedNotification;
use Illuminate\Support\Facades\Notification;

test('listing update mail respects opt-out', function () {
    Notification::fake();

    $publisher = User::factory()->create();
    $subscriber = User::factory()->create(['notify_listing_updates_mail' => false]);

    $subscriber->notify(new ListingUpdatedNotification(new MarketplaceItem(['title' => 'Tagline suite']), 2));

    Notification::assertSentTo($subscriber, ListingUpdatedNotification::class, function ($n, $ch) {
        return $ch === ['database'];
    });
});

test('listing update mail is sent by default', function () {
    Notification::fake();

    $subscriber = User::factory()->create(['notify_listing_updates_mail' => true]);

    $subscriber->notify(new ListingUpdatedNotification(new MarketplaceItem(['title' => 'Tagline suite']), 2));

    Notification::assertSentTo($subscriber, ListingUpdatedNotification::class, function ($n, $ch) {
        return in_array('mail', $ch) && in_array('database', $ch);
    });
});
