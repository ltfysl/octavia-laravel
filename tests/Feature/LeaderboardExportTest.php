<?php

use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\RunStep;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('exports a leaderboard csv for the user', function (): void {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->create();
    $benchmark = Benchmark::factory()->for($user)->create();
    $run = $user->runs()->create([
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'mode' => 'optimize',
        'status' => 'completed',
        'best_score' => 0.95,
        'name' => 'Run',
    ]);
    RunStep::create([
        'run_id' => $run->id,
        'number' => 1,
        'prompt_content' => 'Candidate A',
        'score' => 0.95,
        'phase' => 'mutate',
        'mutation_type' => 'mutate',
    ]);

    $response = $this->actingAs($user)->get('/export/leaderboard');
    $response->assertOk();
    $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
});

it('filters leaderboard csv by benchmark', function (): void {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->create();
    $benchmarkA = Benchmark::factory()->for($user)->create();
    $benchmarkB = Benchmark::factory()->for($user)->create();

    $runA = $user->runs()->create([
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmarkA->id,
        'mode' => 'optimize',
        'status' => 'completed',
        'best_score' => 0.9,
        'name' => 'Run A',
    ]);
    RunStep::create([
        'run_id' => $runA->id,
        'number' => 1,
        'prompt_content' => 'A candidate',
        'score' => 0.9,
        'phase' => 'mutate',
        'mutation_type' => 'mutate',
    ]);

    $runB = $user->runs()->create([
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmarkB->id,
        'mode' => 'optimize',
        'status' => 'completed',
        'best_score' => 0.95,
        'name' => 'Run B',
    ]);
    RunStep::create([
        'run_id' => $runB->id,
        'number' => 1,
        'prompt_content' => 'B candidate',
        'score' => 0.95,
        'phase' => 'mutate',
        'mutation_type' => 'mutate',
    ]);

    $response = $this->actingAs($user)->get("/export/leaderboard?benchmark_id={$benchmarkA->id}");
    $response->assertOk();
    $this->assertStringContainsString('A candidate', $response->streamedContent());
    $this->assertStringNotContainsString('B candidate', $response->streamedContent());
});

it('filters leaderboard csv by run', function (): void {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->create();
    $benchmark = Benchmark::factory()->for($user)->create();

    $run1 = $user->runs()->create([
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'mode' => 'optimize',
        'status' => 'completed',
        'best_score' => 0.8,
        'name' => 'Run 1',
    ]);
    RunStep::create([
        'run_id' => $run1->id,
        'number' => 1,
        'prompt_content' => 'One candidate',
        'score' => 0.8,
        'phase' => 'mutate',
        'mutation_type' => 'mutate',
    ]);

    $run2 = $user->runs()->create([
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'mode' => 'optimize',
        'status' => 'completed',
        'best_score' => 0.85,
        'name' => 'Run 2',
    ]);
    RunStep::create([
        'run_id' => $run2->id,
        'number' => 1,
        'prompt_content' => 'Two candidate',
        'score' => 0.85,
        'phase' => 'mutate',
        'mutation_type' => 'mutate',
    ]);

    $response = $this->actingAs($user)->get("/export/leaderboard?run_id={$run1->id}");
    $response->assertOk();
    $this->assertStringContainsString('One candidate', $response->streamedContent());
    $this->assertStringNotContainsString('Two candidate', $response->streamedContent());
});
