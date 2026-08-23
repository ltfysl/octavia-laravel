<?php

use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('compares two prompt versions against a benchmark', function (): void {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->create();
    $v1 = $prompt->versions()->create(['version' => 1, 'content' => 'Version one', 'changelog' => null]);
    $v2 = $prompt->versions()->create(['version' => 2, 'content' => 'Version two', 'changelog' => null]);
    $benchmark = Benchmark::factory()->for($user)->withContainsCase('test input', 'test input')->create();

    $response = $this->actingAs($user)->postJson("/prompts/{$prompt->id}/ab-test", [
        'version_a_id' => $v1->id,
        'version_b_id' => $v2->id,
        'benchmark_id' => $benchmark->id,
    ]);

    $response->assertOk()->assertJsonStructure([
        'version_a', 'version_b', 'winner', 'benchmark',
    ]);
});

it('rejects ab-test with versions from another prompt', function (): void {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->create();
    $other = Prompt::factory()->for($user)->create();
    $vOther = $other->versions()->create(['version' => 1, 'content' => 'Other version', 'changelog' => null]);
    $vPrompt = $prompt->versions()->create(['version' => 1, 'content' => 'Prompt version', 'changelog' => null]);
    $benchmark = Benchmark::factory()->for($user)->withContainsCase('test input', 'test input')->create();

    $this->actingAs($user)->postJson("/prompts/{$prompt->id}/ab-test", [
        'version_a_id' => $vPrompt->id,
        'version_b_id' => $vOther->id,
        'benchmark_id' => $benchmark->id,
    ])->assertStatus(404);
});
