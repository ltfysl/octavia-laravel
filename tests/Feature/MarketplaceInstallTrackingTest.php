<?php

use App\Enums\MarketplaceItemType;
use App\Models\Benchmark;
use App\Models\MarketplaceItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function publishBenchmarkItem(User $publisher, int $version = 1): MarketplaceItem
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

it('records the installed version on install', function () {
    $publisher = User::factory()->create();
    $buyer = User::factory()->create();
    $item = publishBenchmarkItem($publisher);

    $this->actingAs($buyer)->post("/marketplace/{$item->id}/install")->assertRedirect();

    $install = $buyer->marketplaceInstalls()->first();
    expect($install)->not->toBeNull()
        ->and($install->version)->toBe(1);
});

it('updates the installed version on reinstall', function () {
    $publisher = User::factory()->create();
    $buyer = User::factory()->create();
    $item = publishBenchmarkItem($publisher);

    $this->actingAs($buyer)->post("/marketplace/{$item->id}/install");

    // Publisher bumps the listing version.
    $item->update(['version' => 2]);

    $this->actingAs($buyer)->post("/marketplace/{$item->id}/install");

    expect($buyer->marketplaceInstalls()->first()->version)->toBe(2);
});
