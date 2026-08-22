<?php

use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

function scopedUser(User $user, array $scopes): User
{
    return Sanctum::actingAs($user, $scopes);
}

it('allows prompts:read to read but not write prompts', function () {
    $user = User::factory()->create();
    Prompt::factory()->for($user)->withContent('Mine.')->create();
    scopedUser($user, ['prompts:read']);

    $this->getJson('/api/v1/prompts')->assertOk();
    $this->postJson('/api/v1/prompts', ['name' => 'X', 'content' => 'Y', 'visibility' => 'private'])
        ->assertForbidden();
});

it('implies prompts:read from prompts:write', function () {
    $user = User::factory()->create();
    Prompt::factory()->for($user)->withContent('Mine.')->create();
    scopedUser($user, ['prompts:write']);

    $this->getJson('/api/v1/prompts')->assertOk();
    $this->postJson('/api/v1/prompts', ['name' => 'X', 'content' => 'Y', 'visibility' => 'private'])
        ->assertCreated();
});

it('blocks runs:write tokens from reading prompts', function () {
    $user = User::factory()->create();
    Prompt::factory()->for($user)->withContent('Mine.')->create();
    scopedUser($user, ['runs:write']);

    $this->getJson('/api/v1/prompts')->assertForbidden();
});

it('allows runs:write to start runs and read those runs', function () {
    config(['llm.evolution.max_steps' => 8]);
    $user = User::factory()->create(['credits_balance' => 100]);
    $prompt = Prompt::factory()->for($user)->withContent('Test.')->create();
    $benchmark = Benchmark::factory()->for($user)->withContainsCase('x')->create();
    scopedUser($user, ['runs:write']);

    $response = $this->postJson('/api/v1/runs', [
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'mode' => 'evaluate',
    ])->assertCreated();

    $runId = $response->json('data.id') ?? $response->json('id');
    $this->getJson("/api/v1/runs/{$runId}")->assertOk();
});

it('rejects unknown scopes at token issuance', function () {
    $user = User::factory()->create();

    $this->postJson('/api/v1/auth/token', [
        'email' => $user->email,
        'password' => 'password',
        'abilities' => ['prompts:read', 'teams:delete'],
    ])->assertJsonValidationErrors('abilities.1');
});
