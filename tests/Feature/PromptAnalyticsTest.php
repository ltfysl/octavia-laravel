<?php

use App\Enums\RunMode;
use App\Enums\RunStatus;
use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns prompt analytics for the owner', function (): void {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->create();
    $benchmark = Benchmark::factory()->for($user)->create();
    $user->runs()->create(['prompt_id' => $prompt->id, 'benchmark_id' => $benchmark->id, 'name' => 'Run A', 'mode' => RunMode::Evaluate, 'status' => RunStatus::Completed, 'best_score' => 0.85]);
    $user->runs()->create(['prompt_id' => $prompt->id, 'benchmark_id' => $benchmark->id, 'name' => 'Run B', 'mode' => RunMode::Evaluate, 'status' => RunStatus::Completed, 'best_score' => 0.95]);

    $response = $this->actingAs($user)->getJson("/prompts/{$prompt->id}/analytics");

    $response->assertOk()->assertJsonStructure([
        'runs_count', 'completed_count', 'avg_score', 'best_score', 'history', 'by_benchmark',
    ]);

    expect($response->json('runs_count'))->toBe(2)
        ->and($response->json('avg_score'))->toBe(90)
        ->and($response->json('best_score'))->toBe(95)
        ->and($response->json('history'))->toHaveCount(2)
        ->and($response->json('by_benchmark'))->toHaveCount(1);
});

it('hides analytics from non-owners', function (): void {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $prompt = Prompt::factory()->for($owner)->create();

    $this->actingAs($stranger)->getJson("/prompts/{$prompt->id}/analytics")
        ->assertStatus(403);
});
