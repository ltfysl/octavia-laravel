<?php

use App\Models\MarketplaceItem;
use App\Models\Prompt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('stars and unstars a marketplace item', function (): void {
    $user = User::factory()->create();
    $publisher = User::factory()->create();
    $prompt = Prompt::factory()->for($publisher)->create();
    $item = MarketplaceItem::create([
        'item_type' => 'prompt',
        'prompt_id' => $prompt->id,
        'publisher_id' => $publisher->id,
        'title' => 'Cool prompt',
        'summary' => 'A cool prompt',
        'snapshot' => ['content' => ''],
        'version' => 1,
        'published_at' => now(),
    ]);

    $this->actingAs($user)->postJson("/marketplace/{$item->id}/star")
        ->assertOk()
        ->assertJson(['starred' => true, 'stars_count' => 1]);

    $this->assertDatabaseCount('marketplace_item_stars', 1);

    $this->actingAs($user)->postJson("/marketplace/{$item->id}/star")
        ->assertOk()
        ->assertJson(['starred' => false, 'stars_count' => 0]);

    $this->assertDatabaseCount('marketplace_item_stars', 0);
});

it('forks a marketplace prompt into the user account', function (): void {
    $user = User::factory()->create();
    $publisher = User::factory()->create();
    $prompt = Prompt::factory()->for($publisher)->create();
    $prompt->versions()->create(['version' => 1, 'content' => 'Original content', 'changelog' => null]);
    $item = MarketplaceItem::create([
        'item_type' => 'prompt',
        'prompt_id' => $prompt->id,
        'publisher_id' => $publisher->id,
        'title' => 'Cool prompt',
        'summary' => 'A cool prompt',
        'snapshot' => ['content' => 'Original content'],
        'version' => 1,
        'published_at' => now(),
    ]);

    $response = $this->actingAs($user)->postJson("/marketplace/{$item->id}/fork");

    $response->assertCreated();
    $this->assertDatabaseHas('prompts', ['user_id' => $user->id, 'name' => 'Cool prompt (Fork)']);
    $this->assertDatabaseHas('marketplace_item_forks', ['marketplace_item_id' => $item->id, 'user_id' => $user->id]);
});

it('rejects star on unpublished marketplace item', function (): void {
    $user = User::factory()->create();
    $publisher = User::factory()->create();
    $prompt = Prompt::factory()->for($publisher)->create();
    $item = MarketplaceItem::create([
        'item_type' => 'prompt',
        'prompt_id' => $prompt->id,
        'publisher_id' => $publisher->id,
        'title' => 'Draft',
        'summary' => '',
        'snapshot' => ['content' => ''],
        'version' => 1,
        'published_at' => null,
    ]);

    $this->actingAs($user)->postJson("/marketplace/{$item->id}/star")
        ->assertNotFound();
});
