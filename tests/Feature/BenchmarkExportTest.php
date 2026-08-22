<?php

use App\Models\Benchmark;
use App\Models\User;

it('exports a benchmark as JSON for the owner', function () {
    $user = User::factory()->create();
    $benchmark = Benchmark::factory()->for($user)->hasCasesWithCriteria([
        ['title' => 'Case A', 'input' => 'Do X.', 'criteria' => [
            ['type' => 'contains', 'label' => 'Has X', 'config' => ['values' => ['x']]],
        ]],
    ])->create();

    $response = $this->actingAs($user)->get("/benchmarks/{$benchmark->id}/export");

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/json')
        ->assertJsonPath('name', $benchmark->name)
        ->assertJsonPath('version', 1)
        ->assertJsonCount(1, 'cases');
});

it('rejects export for foreign benchmarks', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $benchmark = Benchmark::factory()->for($owner)->create();

    $this->actingAs($other)->get("/benchmarks/{$benchmark->id}/export")->assertForbidden();
});
