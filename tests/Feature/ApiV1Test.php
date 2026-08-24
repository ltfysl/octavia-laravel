<?php

use App\Jobs\ProcessRunJob;
use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

function apiTokenFor(User $user): string
{
    return $user->createToken('test')->plainTextToken;
}

function apiUserWithAbilities(User $user, array $abilities = ['*']): User
{
    return Sanctum::actingAs($user, $abilities);
}

it('issues a token with valid credentials and rejects bad ones', function () {
    $user = User::factory()->create();

    $this->postJson('/api/v1/auth/token', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertCreated()->assertJsonStructure(['token', 'user']);

    $this->postJson('/api/v1/auth/token', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertJsonValidationErrors('email');
});

it('rejects unknown abilities at token issuance', function () {
    $user = User::factory()->create();

    $this->postJson('/api/v1/auth/token', [
        'email' => $user->email,
        'password' => 'password',
        'abilities' => ['read', 'delete-everything'],
    ])->assertJsonValidationErrors('abilities.1');
});

it('requires authentication for API endpoints', function () {
    $this->getJson('/api/v1/prompts')->assertUnauthorized();
});

it('lists and shows prompts via the API', function () {
    $user = User::factory()->create();
    Prompt::factory()->for($user)->withContent('API content.')->create();
    Prompt::factory()->withContent('Not mine.')->create();

    apiUserWithAbilities($user);
    $response = $this->getJson('/api/v1/prompts');

    $response->assertOk();
    expect($response->json('meta.total'))->toBe(1)
        ->and($response->json('data.0.content'))->toBeNull(); // content only on show

    $prompt = $user->prompts()->first();
    $show = $this->getJson("/api/v1/prompts/{$prompt->id}");
    expect($show->json('data.content'))->toBe('API content.');
});

it('creates prompts via the API', function () {
    $user = User::factory()->create();

    apiUserWithAbilities($user, ['write']);
    $this->postJson('/api/v1/prompts', [
        'name' => 'API prompt',
        'visibility' => 'private',
        'content' => 'You are an API-created prompt.',
    ])->assertCreated()->assertJsonPath('data.version', 1);

    expect($user->prompts()->first()->currentVersion->content)->toBe('You are an API-created prompt.');
});

it('blocks write endpoints for read-only tokens', function () {
    $user = User::factory()->create();

    apiUserWithAbilities($user, ['read']);
    $this->postJson('/api/v1/prompts', [
        'name' => 'Blocked',
        'visibility' => 'private',
        'content' => 'Should not persist.',
    ])->assertForbidden();

    expect($user->prompts()->count())->toBe(0);
});

it('evaluates synchronously against a benchmark without creating a run', function () {
    Queue::fake();

    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->withContent("- Include the phrase eco friendly\nYou are a marketing assistant.")->create();
    $benchmark = Benchmark::factory()->for($user)->hasCasesWithCriteria([
        ['title' => 'Eco', 'input' => 'Write about a bottle.', 'criteria' => [
            ['type' => 'contains', 'label' => '- eco friendly', 'config' => ['values' => ['eco friendly']]],
        ]],
    ])->create();

    apiUserWithAbilities($user, ['read']);

    $response = $this->postJson("/api/v1/prompts/{$prompt->id}/evaluate", [
        'benchmark_id' => $benchmark->id,
    ]);

    $response->assertOk()->assertJsonStructure(['score', 'cases']);
    expect($response->json('score'))->toBe(1)
        ->and(Queue::pushedJobs(ProcessRunJob::class))->toBeEmpty();
});

it('starts and cancels runs via the API with ownership enforced', function () {
    Queue::fake();

    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->withContent('Test.')->create();
    $benchmark = Benchmark::factory()->for($user)->withContainsCase('x')->create();

    apiUserWithAbilities($user, ['read', 'write']);

    $runId = $this->postJson('/api/v1/runs', [
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'mode' => 'evaluate',
    ])->assertCreated()->json('data.id');

    $other = User::factory()->create();
    apiUserWithAbilities($other, ['read', 'write']);
    $this->getJson("/api/v1/runs/{$runId}")->assertNotFound();

    apiUserWithAbilities($user, ['read', 'write']);
    $this->postJson("/api/v1/runs/{$runId}/cancel")->assertOk();
});

it('blocks run creation for read-only tokens', function () {
    Queue::fake();

    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->withContent('Test.')->create();
    $benchmark = Benchmark::factory()->for($user)->withContainsCase('x')->create();

    apiUserWithAbilities($user, ['read']);

    $this->postJson('/api/v1/runs', [
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'mode' => 'evaluate',
    ])->assertForbidden();

    Queue::assertNothingPushed();
});

it('duplicates a prompt via the API', function () {
    $user = User::factory()->create(['credits_balance' => 10]);
    $prompt = Prompt::factory()->for($user)->create();
    $prompt->versions()->create(['version' => 1, 'content' => 'Original.', 'changelog' => 'Initial']);
    $prompt->update(['current_version_id' => $prompt->versions->first()->id]);

    apiUserWithAbilities($user, ['read', 'write']);

    $this->postJson("/api/v1/prompts/{$prompt->id}/duplicate")
        ->assertCreated()
        ->assertJsonPath('data.name', $prompt->name.' (copy)');
});

it('rejects prompt duplication without write scope', function () {
    $user = User::factory()->create(['credits_balance' => 10]);
    $prompt = Prompt::factory()->for($user)->create();

    apiUserWithAbilities($user, ['read']);

    $this->postJson("/api/v1/prompts/{$prompt->id}/duplicate")
        ->assertForbidden();
});
