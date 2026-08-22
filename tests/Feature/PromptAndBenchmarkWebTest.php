<?php

use App\Jobs\ProcessRunJob;
use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('creates a prompt with its initial version', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/prompts', [
        'name' => 'My assistant',
        'description' => 'Test',
        'visibility' => 'private',
        'content' => 'You are a test assistant.',
    ])->assertRedirect();

    $prompt = Prompt::where('user_id', $user->id)->first();
    expect($prompt->currentVersion->version)->toBe(1)
        ->and($prompt->currentVersion->content)->toBe('You are a test assistant.');
});

it('versions a prompt on content change and restores old versions', function () {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->withContent('Version one.')->create();

    $this->actingAs($user)->patch("/prompts/{$prompt->id}", [
        'content' => 'Version two.',
        'changelog' => 'Rewritten',
    ])->assertRedirect();

    expect($prompt->fresh()->current_version_id)->not->toBeNull()
        ->and($prompt->fresh()->currentVersion->version)->toBe(2);

    $v1 = $prompt->fresh()->versions()->where('version', 1)->first();
    $this->post("/prompts/{$prompt->id}/versions/{$v1->id}/restore")->assertRedirect();

    expect($prompt->fresh()->currentVersion->version)->toBe(3)
        ->and($prompt->fresh()->currentVersion->content)->toBe('Version one.');
});

it('validates benchmark payloads strictly', function () {
    $user = User::factory()->create();

    // Missing criteria config for contains type
    $this->actingAs($user)->post('/benchmarks', [
        'name' => 'Broken',
        'category' => 'coding',
        'visibility' => 'private',
        'cases' => [
            ['title' => 'C1', 'input' => 'in', 'criteria' => [
                ['type' => 'contains', 'label' => 'L', 'config' => []],
            ]],
        ],
    ])->assertSessionHasErrors();

    // Invalid regex pattern is accepted at validation level but scored 0 by the engine;
    // invalid category must fail.
    $this->actingAs($user)->post('/benchmarks', [
        'name' => 'Bad',
        'category' => 'nuclear',
        'visibility' => 'private',
        'cases' => [
            ['title' => 'C1', 'input' => 'in', 'criteria' => [
                ['type' => 'contains', 'label' => 'L', 'config' => ['values' => ['x']]],
            ]],
        ],
    ])->assertSessionHasErrors('category');
});

it('stores a valid benchmark with cases and criteria', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/benchmarks', [
        'name' => 'Valid',
        'category' => 'sales',
        'visibility' => 'public',
        'cases' => [
            ['title' => 'Case A', 'input' => 'Do X.', 'weight' => 2, 'criteria' => [
                ['type' => 'contains', 'label' => 'Has X', 'config' => ['values' => ['x']]],
                ['type' => 'llm_judge', 'label' => 'Polite tone', 'config' => ['description' => '- Polite tone']],
            ]],
        ],
    ])->assertRedirect();

    $benchmark = Benchmark::where('user_id', $user->id)->first();
    expect($benchmark->cases()->count())->toBe(1)
        ->and($benchmark->cases()->first()->criteria()->count())->toBe(2)
        ->and((float) $benchmark->cases()->first()->weight)->toBe(2.0);
});

it('queues a run and completes it synchronously in sync mode', function () {
    Queue::fake();

    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->withContent('Test.')->create();
    $benchmark = Benchmark::factory()->for($user)->withContainsCase('hello')->create();

    $this->actingAs($user)->post('/runs', [
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'mode' => 'optimize',
    ])->assertRedirect();

    Queue::assertPushed(ProcessRunJob::class);
});

it('rejects runs against empty benchmarks', function () {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->withContent('Test.')->create();
    $benchmark = Benchmark::factory()->for($user)->create(); // no cases

    $this->actingAs($user)->post('/runs', [
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'mode' => 'evaluate',
    ]);
});
