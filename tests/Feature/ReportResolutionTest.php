<?php

use App\Enums\MarketplaceItemType;
use App\Models\MarketplaceItem;
use App\Models\MarketplaceReport;
use App\Models\Prompt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function publishedItem(User $publisher): MarketplaceItem
{
    $prompt = Prompt::factory()->for($publisher)->withContent('Shared.')->create();

    return MarketplaceItem::create([
        'item_type' => MarketplaceItemType::Prompt,
        'prompt_id' => $prompt->id,
        'publisher_id' => $publisher->id,
        'title' => $prompt->name,
        'version' => 1,
        'published_at' => now(),
    ]);
}

it('notifies the reporter when their report is resolved', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $publisher = User::factory()->create();
    $reporter = User::factory()->create();
    $item = publishedItem($publisher);

    $this->actingAs($reporter)->post("/marketplace/{$item->id}/report", ['reason' => 'spam']);

    $report = MarketplaceReport::first();
    $this->actingAs($admin)->post("/admin/reports/{$report->id}/resolve/unlist");

    expect($reporter->notifications()->count())->toBe(1);

    $notification = $reporter->unreadNotifications->first();
    expect(data_get($notification, 'data.item_title'))->toBe($item->title)
        ->and(data_get($notification, 'data.outcome'))->toBe('unlisted');
});

it('records the resolver actor', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $user = User::factory()->create();
    $item = publishedItem($user);

    $this->actingAs($user)->post("/marketplace/{$item->id}/report", ['reason' => 'other']);

    $report = MarketplaceReport::first();
    $this->actingAs($admin)->post("/admin/reports/{$report->id}/resolve/dismiss");

    expect($report->fresh()->resolved_by)->toBe($admin->id)
        ->and($report->fresh()->status)->toBe('resolved');
});
