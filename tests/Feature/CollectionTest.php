<?php

use App\Jobs\ProcessRunJob;
use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('creates a collection scoped to the owner benchmarks', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $mine = Benchmark::factory()->for($user)->withContainsCase('x')->create();
    $foreign = Benchmark::factory()->for($other)->withContainsCase('y')->create();

    $this->actingAs($user)->post('/collections', [
        'name' => 'My suite',
        'benchmark_ids' => [$mine->id, $foreign->id],
    ])->assertSessionHasErrors('benchmark_ids.1');

    $this->actingAs($user)->post('/collections', [
        'name' => 'My suite',
        'benchmark_ids' => [$mine->id],
    ])->assertRedirect();

    $collection = $user->collections()->first();
    expect($collection->benchmarks()->pluck('benchmarks.id'))->toContain($mine->id);
});

it('runs a prompt against an entire collection', function () {
    Queue::fake();

    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->withContent('Test.')->create();
    $b1 = Benchmark::factory()->for($user)->withContainsCase('a')->create();
    $b2 = Benchmark::factory()->for($user)->withContainsCase('b')->create();

    $this->actingAs($user)->post('/collections', [
        'name' => 'Suite',
        'benchmark_ids' => [$b1->id, $b2->id],
    ]);

    $collection = $user->collections()->first();

    $this->actingAs($user)->post('/runs', [
        'prompt_id' => $prompt->id,
        'collection_id' => $collection->id,
        'mode' => 'evaluate',
    ])->assertRedirect();

    Queue::assertPushed(ProcessRunJob::class);
    expect($user->runs()->first()->collection_id)->toBe($collection->id)
        ->and($user->runs()->first()->benchmark_id)->toBeNull();
});
