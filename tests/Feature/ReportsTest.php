<?php

use App\Enums\RunMode;
use App\Enums\RunStatus;
use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the reports page with aggregates', function (): void {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->withContent('Write a tagline.')->create();
    $benchmark = Benchmark::factory()->for($user)->withContainsCase('brand')->create();

    $user->runs()->create([
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'name' => 'Test run A',
        'mode' => RunMode::Evaluate,
        'status' => RunStatus::Completed,
        'best_score' => 0.85,
    ]);
    $user->runs()->create([
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'name' => 'Test run B',
        'mode' => RunMode::Optimize,
        'status' => RunStatus::Completed,
        'best_score' => 0.95,
    ]);

    $response = $this->actingAs($user)->get('/reports');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('reports/Index')
        ->has('stats')
        ->where('stats.total', 2)
        ->has('byBenchmark')
        ->has('byPrompt')
    );
});

it('hides reports from guests', function (): void {
    $this->get('/reports')->assertRedirect('/login');
});
