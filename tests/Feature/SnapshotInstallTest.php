<?php

use App\Enums\MarketplaceItemType;
use App\Models\Benchmark;
use App\Models\MarketplaceItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function publishWithSnapshot(User $publisher): array
{
    $benchmark = Benchmark::factory()->for($publisher)->hasCasesWithCriteria([
        ['title' => 'Original case', 'input' => 'original input', 'criteria' => [
            ['type' => 'contains', 'label' => '- has original', 'config' => ['values' => ['original']]],
        ]],
    ])->create();

    $item = MarketplaceItem::create([
        'item_type' => MarketplaceItemType::Benchmark,
        'benchmark_id' => $benchmark->id,
        'publisher_id' => $publisher->id,
        'title' => $benchmark->name,
        'version' => $benchmark->fresh()->version ?? 1,
        'snapshot' => [
            'category' => $benchmark->category->value,
            'cases' => [[
                'title' => 'Original case',
                'input' => 'original input',
                'weight' => 1.0,
                'criteria' => [['type' => 'contains', 'label' => '- has original', 'config' => ['values' => ['original']]]],
            ]],
        ],
        'published_at' => now(),
    ]);

    return [$item, $benchmark];
}

it('freezes a benchmark snapshot at publish time', function () {
    $publisher = User::factory()->create();
    [$item] = publishWithSnapshot($publisher);

    expect(data_get($item->snapshot, 'cases.0.title'))->toBe('Original case');
});

it('installs the snapshotted version even after the source changes', function () {
    $publisher = User::factory()->create();
    $buyer = User::factory()->create();
    [$item, $benchmark] = publishWithSnapshot($publisher);

    // Publisher mutates the live benchmark after publishing.
    $benchmark->cases->first()->update(['title' => 'Mutated title']);
    $benchmark->cases()->first()->criteria()->delete();
    $benchmark->cases()->first()->criteria()->create([
        'type' => 'contains', 'label' => '- mutated', 'config' => ['values' => ['mutated']], 'position' => 0,
    ]);

    $this->actingAs($buyer)->post("/marketplace/{$item->id}/install")->assertRedirect();

    $copy = Benchmark::where('user_id', $buyer->id)->first();
    expect($copy->cases()->count())->toBe(1)
        ->and($copy->cases()->first()->title)->toBe('Original case')
        ->and($copy->cases()->first()->criteria()->count())->toBe(1)
        ->and($copy->cases()->first()->criteria()->first()->config['values'][0])->toBe('original');
});

it('falls back to the live suite when no snapshot exists (legacy listings)', function () {
    $publisher = User::factory()->create();
    $buyer = User::factory()->create();
    $benchmark = Benchmark::factory()->for($publisher)->withContainsCase('legacy')->create();

    $item = MarketplaceItem::create([
        'item_type' => MarketplaceItemType::Benchmark,
        'benchmark_id' => $benchmark->id,
        'publisher_id' => $publisher->id,
        'title' => $benchmark->name,
        'version' => 1,
        'snapshot' => null, // legacy listing without snapshot
        'published_at' => now(),
    ]);

    $this->actingAs($buyer)->post("/marketplace/{$item->id}/install")->assertRedirect();

    $copy = Benchmark::where('user_id', $buyer->id)->first();
    expect($copy->cases()->count())->toBe(1);
});
