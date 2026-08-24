<?php

use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('runs a regression test against the prompt category', function (): void {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->create(['category' => 'general']);
    Benchmark::factory()->for($user)->withContainsCase('test input', 'test input')->create(['category' => 'general']);

    $response = $this->actingAs($user)->postJson("/prompts/{$prompt->id}/regression-test");

    $response->assertOk()->assertJsonStructure([
        'results',
        'summary' => ['total', 'passed', 'failed', 'errors', 'avg_score'],
    ]);
});

it('rejects regression test for a prompt the user does not own', function (): void {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $prompt = Prompt::factory()->for($owner)->create();

    $this->actingAs($other)->postJson("/prompts/{$prompt->id}/regression-test")
        ->assertStatus(403);
});

it('validates benchmark ids belong to the user', function (): void {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->create();
    $other = User::factory()->create();
    $benchmark = Benchmark::factory()->for($other)->create();

    $this->actingAs($user)->postJson("/prompts/{$prompt->id}/regression-test", [
        'benchmark_ids' => [$benchmark->id],
    ])->assertStatus(422);
});
