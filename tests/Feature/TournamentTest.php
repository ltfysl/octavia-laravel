<?php

use App\Jobs\ProcessRunJob;
use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function () {
    config()->set('llm.providers.openai.key', 'sk-test-key-for-tournament');
    Queue::fake();
});

it('creates one run per configured provider and redirects to ranking', function () {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->withContent('Say hi.')->create();
    $benchmark = Benchmark::factory()->for($user)->withContainsCase('hi')->create();

    $response = $this->actingAs($user)->post('/tournaments', [
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'providers' => ['mock', 'openai', 'unknown-provider'],
    ]);

    $runIds = $user->runs()->pluck('id');

    expect($runIds)->toHaveCount(2)
        ->and($user->runs()->pluck('provider')->sort()->values()->all())->toBe(['mock', 'openai'])
        ->and($user->runs()->where('mode', 'evaluate')->count())->toBe(2);

    Queue::assertPushed(ProcessRunJob::class, 2);

    $response->assertRedirect(route('tournaments.index', ['runs' => $runIds->implode(',')]));
});

it('rejects a tournament when fewer than two providers are configured', function () {
    config()->set('llm.providers.openai.key', null);

    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->withContent('Say hi.')->create();
    $benchmark = Benchmark::factory()->for($user)->withContainsCase('hi')->create();

    $this->actingAs($user)
        ->from(route('tournaments.index'))
        ->post('/tournaments', [
            'prompt_id' => $prompt->id,
            'benchmark_id' => $benchmark->id,
            'providers' => ['mock'],
        ])
        ->assertSessionHasErrors('providers');

    expect($user->runs()->count())->toBe(0);
});

it('ranks tournament runs by best score for the owner only', function () {
    $user = User::factory()->create();
    $stranger = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->withContent('Say hi.')->create();
    $benchmark = Benchmark::factory()->for($user)->withContainsCase('hi')->create();

    $a = $user->runs()->create([
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'name' => 'A · mock',
        'mode' => 'evaluate',
        'status' => 'completed',
        'provider' => 'mock',
        'best_score' => 0.4,
        'max_steps' => 1,
        'target_score' => 0.95,
    ]);
    $b = $user->runs()->create([
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'name' => 'B · openai',
        'mode' => 'evaluate',
        'status' => 'completed',
        'provider' => 'openai',
        'best_score' => 0.9,
        'max_steps' => 1,
        'target_score' => 0.95,
    ]);
    $foreign = $stranger->runs()->create([
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'name' => 'Foreign run',
        'mode' => 'evaluate',
        'status' => 'completed',
        'provider' => 'mock',
        'best_score' => 1.0,
        'max_steps' => 1,
        'target_score' => 0.95,
    ]);

    $response = $this->actingAs($user)->get("/tournaments?runs={$b->id},{$a->id},{$foreign->id}");

    $response->assertOk()->assertInertia(function ($page) use ($a, $b) {
        $page->component('tournaments/Index')
            ->where('results.0.id', $b->id)      // 0.9 ranks above 0.4
            ->where('results.1.id', $a->id)      // despite query order
            ->missing('results.2');              // foreign run is filtered out
    });
});
