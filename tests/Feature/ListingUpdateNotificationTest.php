<?php

use App\Enums\MarketplaceItemType;
use App\Models\Benchmark;
use App\Models\MarketplaceItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function publishListing(User $publisher, int $version = 1): MarketplaceItem
{
    $benchmark = Benchmark::factory()->for($publisher)->withContainsCase('x')->create();

    return MarketplaceItem::create([
        'item_type' => MarketplaceItemType::Benchmark,
        'benchmark_id' => $benchmark->id,
        'publisher_id' => $publisher->id,
        'title' => $benchmark->name,
        'version' => $version,
        'snapshot' => ['category' => 'general', 'cases' => []],
        'published_at' => now(),
    ]);
}

it('notifies installed users when a listing is republished at a new version', function () {
    $publisher = User::factory()->create();
    $buyer = User::factory()->create();
    $item = publishListing($publisher);

    $this->actingAs($buyer)->post("/marketplace/{$item->id}/install")->assertRedirect();

    // Publisher bumps the version via republish.
    // Publisher mutates the benchmark and republishes at a new version.
    $item->benchmark->bumpVersion();
    $this->actingAs($publisher)->post('/marketplace/publish', [
        'item_type' => 'benchmark',
        'item_id' => $item->benchmark_id,
    ]);

    expect($buyer->notifications()->count())->toBe(1)
        ->and(data_get($buyer->notifications->first(), 'data.new_version'))->toBe($item->fresh()->version);
});

it('does not notify the publisher about their own listing', function () {
    $publisher = User::factory()->create();

    // Publisher installs their own listing (edge case) and republishes.
    $item = publishListing($publisher);
    $publisher->marketplaceInstalls()->create([
        'marketplace_item_id' => $item->id,
        'version' => 1,
    ]);

    $this->actingAs($publisher)->post('/marketplace/publish', [
        'item_type' => 'benchmark',
        'item_id' => $item->benchmark_id,
    ])->assertRedirect();

    expect($publisher->notifications()->count())->toBe(0);
});
