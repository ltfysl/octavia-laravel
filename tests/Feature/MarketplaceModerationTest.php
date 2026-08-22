<?php

use App\Enums\MarketplaceItemType;
use App\Models\Benchmark;
use App\Models\MarketplaceItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function publishBenchmark(User $user): MarketplaceItem
{
    $benchmark = Benchmark::factory()->for($user)->withContainsCase('x')->create();

    return MarketplaceItem::create([
        'item_type' => MarketplaceItemType::Benchmark,
        'benchmark_id' => $benchmark->id,
        'publisher_id' => $user->id,
        'title' => $benchmark->name,
        'version' => 1,
        'published_at' => now(),
    ]);
}

it('forbids non-admins from moderation endpoints', function () {
    $user = User::factory()->create();
    $item = publishBenchmark($user);

    $this->actingAs($user)->get('/admin/marketplace')->assertForbidden();
    $this->actingAs($user)->post("/admin/marketplace/{$item->id}/listed", ['listed' => false])->assertForbidden();
});

it('lists published and unlisted items for admins', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $user = User::factory()->create();
    publishBenchmark($user);

    $this->actingAs($admin)->get('/admin/marketplace')->assertOk();
});

it('unlists and relists items via moderation', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $user = User::factory()->create();
    $item = publishBenchmark($user);

    // Unlist: hidden from the public marketplace.
    $this->actingAs($admin)->post("/admin/marketplace/{$item->id}/listed", ['listed' => false])
        ->assertRedirect();

    expect($item->fresh()->published_at)->toBeNull();
    $this->get('/marketplace')->assertOk();

    // Relist: visible again.
    $this->actingAs($admin)->post("/admin/marketplace/{$item->id}/listed", ['listed' => true])
        ->assertRedirect();

    expect($item->fresh()->published_at)->not->toBeNull();
});

it('blocks installing unlisted items and restores on relist', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $user = User::factory()->create();
    $buyer = User::factory()->create();
    $item = publishBenchmark($user);

    $this->actingAs($admin)->post("/admin/marketplace/{$item->id}/listed", ['listed' => false]);

    // Unlisted items disappear from the public listing and cannot be installed.
    $this->actingAs($buyer)->post("/marketplace/{$item->id}/install")->assertNotFound();

    // Relisting restores installability.
    $this->actingAs($admin)->post("/admin/marketplace/{$item->id}/listed", ['listed' => true]);
    $this->actingAs($buyer)->post("/marketplace/{$item->id}/install")->assertRedirect();
});
