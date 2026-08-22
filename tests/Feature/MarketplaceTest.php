<?php

use App\Models\Benchmark;
use App\Models\MarketplaceItem;
use App\Models\Prompt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('publishes a prompt to the marketplace and another user can install it', function () {
    $publisher = User::factory()->create();
    $buyer = User::factory()->create();

    $prompt = Prompt::factory()->for($publisher)->withContent('You are a shared assistant.')->create();
    $prompt->versions()->create(['version' => 2, 'content' => 'Improved version.', 'created_at' => now()]);
    $prompt->update(['current_version_id' => $prompt->versions()->first()->id]);

    $this->actingAs($publisher)->post('/marketplace/publish', [
        'item_type' => 'prompt',
        'item_id' => $prompt->id,
        'summary' => 'A helpful prompt',
    ])->assertRedirect();

    $item = MarketplaceItem::first();
    expect($item->publisher_id)->toBe($publisher->id)
        ->and($item->title)->toBe($prompt->name)
        ->and($item->published_at)->not->toBeNull();

    $this->actingAs($buyer)->post("/marketplace/{$item->id}/install")->assertRedirect();

    $copy = Prompt::where('user_id', $buyer->id)->first();
    expect($copy)->not->toBeNull()
        // Install yields the frozen published content as a single version.
        ->and($copy->versions()->count())->toBe(1)
        ->and($copy->currentVersion->content)->toBe('Improved version.')
        ->and($copy->user_id)->toBe($buyer->id)
        ->and($copy->visibility->value)->toBe('private');
});

it('publishes a benchmark and install copies cases and criteria', function () {
    $publisher = User::factory()->create();
    $buyer = User::factory()->create();

    $benchmark = Benchmark::factory()->for($publisher)->hasCasesWithCriteria([
        ['title' => 'Case 1', 'input' => 'in', 'criteria' => [
            ['type' => 'contains', 'label' => 'Has x', 'config' => ['values' => ['x']]],
            ['type' => 'regex', 'label' => 'Digits', 'config' => ['pattern' => '/[0-9]/']],
        ]],
    ])->create();

    $this->actingAs($publisher)->post('/marketplace/publish', [
        'item_type' => 'benchmark',
        'item_id' => $benchmark->id,
    ])->assertRedirect();

    $item = MarketplaceItem::first();
    $this->actingAs($buyer)->post("/marketplace/{$item->id}/install")->assertRedirect();

    $copy = Benchmark::where('user_id', $buyer->id)->first();
    expect($copy->cases()->count())->toBe(1)
        ->and($copy->cases()->first()->criteria()->count())->toBe(2)
        ->and((int) MarketplaceItem::first()->downloads)->toBe(1);
});

it('validates publish payloads', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/marketplace/publish', [
        'item_type' => 'widget',
        'item_id' => 999,
    ])->assertSessionHasErrors('item_type');
});
