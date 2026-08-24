<?php

use App\Models\Benchmark;
use App\Models\User;

test('owner can duplicate a benchmark with cases and criteria', function () {
    $user = User::factory()->create();
    $benchmark = Benchmark::factory()->for($user)->create(['name' => 'Original']);
    $case = $benchmark->cases()->create([
        'title' => 'Case 1',
        'input' => 'input',
        'weight' => 2,
        'position' => 0,
    ]);
    $case->criteria()->create([
        'type' => 'contains',
        'label' => 'Has word',
        'config' => ['values' => ['ok']],
        'position' => 0,
    ]);

    $this->actingAs($user)->post("/benchmarks/{$benchmark->id}/duplicate")
        ->assertRedirect();

    $copy = $user->fresh()->benchmarks()->latest('id')->first();
    expect($copy->id)->not->toBe($benchmark->id)
        ->and($copy->name)->toContain('copy')
        ->and($copy->cases->first()->title)->toBe('Case 1')
        ->and($copy->cases->first()->criteria->first()->label)->toBe('Has word');
});

test('strangers cannot duplicate a private benchmark', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $benchmark = Benchmark::factory()->for($owner)->create(['visibility' => 'private']);

    $this->actingAs($stranger)->post("/benchmarks/{$benchmark->id}/duplicate")
        ->assertNotFound();
});
