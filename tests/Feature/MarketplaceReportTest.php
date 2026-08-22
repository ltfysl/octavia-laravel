<?php

use App\Enums\MarketplaceItemType;
use App\Models\MarketplaceItem;
use App\Models\MarketplaceReport;
use App\Models\Prompt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function publishedPromptItem(User $publisher): MarketplaceItem
{
    $prompt = Prompt::factory()->for($publisher)->withContent('Shared prompt.')->create();

    return MarketplaceItem::create([
        'item_type' => MarketplaceItemType::Prompt,
        'prompt_id' => $prompt->id,
        'publisher_id' => $publisher->id,
        'title' => $prompt->name,
        'version' => 1,
        'published_at' => now(),
    ]);
}

it('requires authentication to report', function () {
    $publisher = User::factory()->create();
    $item = publishedPromptItem($publisher);

    $this->post("/marketplace/{$item->id}/report", ['reason' => 'spam'])
        ->assertRedirect('/login');
});

it('validates report reasons', function () {
    $user = User::factory()->create();
    $item = publishedPromptItem($user);

    $this->actingAs($user)->post("/marketplace/{$item->id}/report", [
        'reason' => 'vibes',
    ])->assertSessionHasErrors('reason');
});

it('creates a report and deduplicates per user', function () {
    $publisher = User::factory()->create();
    $reporter = User::factory()->create();
    $item = publishedPromptItem($publisher);

    $this->actingAs($reporter)->post("/marketplace/{$item->id}/report", ['reason' => 'spam', 'message' => 'First'])
        ->assertRedirect();
    $this->actingAs($reporter)->post("/marketplace/{$item->id}/report", ['reason' => 'spam', 'message' => 'Second'])
        ->assertRedirect();

    expect(MarketplaceReport::count())->toBe(1)
        ->and(MarketplaceReport::first()->message)->toBe('Second');
});

it('lets admins see open reports and resolve or unlist them', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $publisher = User::factory()->create();
    $reporter = User::factory()->create();
    $item = publishedPromptItem($publisher);

    $this->actingAs($reporter)->post("/marketplace/{$item->id}/report", [
        'reason' => 'copyright',
        'message' => 'This is stolen work.',
    ]);

    // Admin sees the open report.
    $this->actingAs($admin)->get('/admin/reports')->assertOk();

    // Unlist-and-resolve: item hidden, report resolved with actor.
    $report = MarketplaceReport::first();
    $this->actingAs($admin)->post("/admin/reports/{$report->id}/resolve/unlist")
        ->assertRedirect();

    expect($item->fresh()->published_at)->toBeNull()
        ->and($report->fresh()->status)->toBe('resolved')
        ->and($report->fresh()->resolved_by)->toBe($admin->id);

    // The item is now unlisted: further reports are rejected (404) and
    // the listing stays hidden.
    $this->actingAs($reporter)->post("/marketplace/{$item->id}/report", ['reason' => 'other'])
        ->assertNotFound();

    expect($item->fresh()->published_at)->toBeNull();
});
