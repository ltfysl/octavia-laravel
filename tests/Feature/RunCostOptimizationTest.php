<?php

use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('uses distinct evaluation and mutation models when cost optimization is enabled', function (): void {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->create();
    $benchmark = Benchmark::factory()->for($user)->create();

    $run = $user->runs()->create([
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'name' => 'Cost optimized run',
        'mode' => 'optimize',
        'status' => 'pending',
        'provider' => 'mock',
        'model' => 'strong-model',
        'cost_optimized' => true,
        'max_steps' => config('llm.evolution.max_steps'),
        'target_score' => config('llm.evolution.target_score'),
    ]);

    expect($run->usesCostOptimization())->toBeTrue();
    expect($run->evaluationModel())->toBe('strong-model');
    expect($run->mutationModel())->toBe(config('llm.cost_optimized.mutation_model'));
});

it('falls back to provider model when run model is not set', function (): void {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->create();
    $benchmark = Benchmark::factory()->for($user)->create();

    $run = $user->runs()->create([
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'name' => 'Default model run',
        'mode' => 'evaluate',
        'status' => 'pending',
        'provider' => 'mock',
        'max_steps' => 1,
        'target_score' => 0.95,
    ]);

    expect($run->evaluationModel())->toBe('');
    expect($run->usesCostOptimization())->toBeFalse();
});
