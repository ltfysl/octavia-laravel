<?php

use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lists activity for authenticated user', function (): void {
    $user = User::factory()->create();
    Prompt::factory()->for($user)->create(['name' => 'Test prompt']);
    Benchmark::factory()->for($user)->create(['name' => 'Test benchmark']);
    $prompt = Prompt::factory()->for($user)->create();
    $benchmark = Benchmark::factory()->for($user)->create();
    $user->runs()->create([
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'mode' => 'evaluate',
        'status' => 'completed',
        'best_score' => 0.85,
        'name' => 'Test run',
    ]);

    $this->actingAs($user)->get('/activity')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('items'));
});

it('exposes activity via api', function (): void {
    $user = User::factory()->create();
    $token = $user->createToken('test', ['read'])->plainTextToken;

    $this->withHeaders(['Authorization' => 'Bearer '.$token])
        ->getJson('/api/v1/activity')
        ->assertOk()
        ->assertJsonStructure(['items']);
});
