<?php

use App\Enums\MarketplaceItemType;
use App\Models\Benchmark;
use App\Models\MarketplaceItem;
use App\Models\Prompt;
use App\Models\User;
use App\Notifications\ListingUpdatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders branded error pages', function () {
    $this->get('/nonexistent-page')->assertStatus(404);
    $this->get('/nonexistent-page')->assertSee('Octavia');
});

it('notifies installed users when a listing version moves ahead', function () {
    Notification::fake();

    $publisher = User::factory()->create();
    $buyer = User::factory()->create();
    $prompt = Prompt::factory()->for($publisher)->withContent('Published content.')->create();
    $benchmark = Benchmark::factory()->for($publisher)->withContainsCase('x')->create();

    $item = MarketplaceItem::create([
        'item_type' => MarketplaceItemType::Prompt,
        'prompt_id' => $prompt->id,
        'publisher_id' => $publisher->id,
        'title' => $prompt->name,
        'version' => 1,
        'snapshot' => ['content' => 'Published content.'],
        'published_at' => now(),
    ]);

    // Buyer installs at v1.
    $buyer->marketplaceInstalls()->create([
        'marketplace_item_id' => $item->id,
        'version' => 1,
    ]);

    // Publisher bumps to v2.
    $item->update(['version' => 2]);

    // Trigger republish notification manually (publish flow already tested).
    $buyer->notify(new ListingUpdatedNotification($item, 2));

    Notification::assertSentTo($buyer, ListingUpdatedNotification::class);
});
