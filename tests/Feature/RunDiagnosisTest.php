<?php

use App\Enums\RunMode;
use App\Enums\RunStatus;
use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('diagnoses a failed run', function (): void {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->withContent('Write a tagline.')->create();
    $benchmark = Benchmark::factory()->for($user)->withContainsCase('brand')->create();
    $run = $user->runs()->create([
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'name' => 'Failing run',
        'mode' => RunMode::Evaluate,
        'status' => RunStatus::Failed,
        'best_score' => 0.2,
    ]);

    $response = $this->actingAs($user)->postJson("/runs/{$run->id}/diagnosis");

    $response->assertOk()
        ->assertJsonStructure(['diagnosis', 'tokens']);

    expect($response->json('diagnosis'))->toContain('Next step');
});

it('denies diagnosis to non-owners', function (): void {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $prompt = Prompt::factory()->for($owner)->withContent('Write a tagline.')->create();
    $benchmark = Benchmark::factory()->for($owner)->withContainsCase('brand')->create();
    $run = $owner->runs()->create([
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'name' => 'x',
        'mode' => RunMode::Evaluate,
        'status' => RunStatus::Failed,
    ]);

    $this->actingAs($stranger)->postJson("/runs/{$run->id}/diagnosis")
        ->assertStatus(404);
});

it('rejects diagnosis for completed runs', function (): void {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->withContent('Write a tagline.')->create();
    $benchmark = Benchmark::factory()->for($user)->withContainsCase('brand')->create();
    $run = $user->runs()->create([
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'name' => 'x',
        'mode' => RunMode::Evaluate,
        'status' => RunStatus::Completed,
    ]);

    $this->actingAs($user)->postJson("/runs/{$run->id}/diagnosis")
        ->assertOk()
        ->assertJsonFragment(['diagnosis' => 'Diagnosis is only available for failed or cancelled runs.']);
});
