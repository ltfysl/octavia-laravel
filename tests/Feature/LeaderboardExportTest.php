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
