<?php

use App\Enums\RunStatus;
use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\User;
use App\Services\EvaluationService;
use App\Services\EvolutionService;
use App\Services\Llm\LlmManager;

it('scores a prompt against contains criteria', function () {
    $benchmark = Benchmark::factory()->withContainsCase('hello world')->create();

    $evaluation = app(EvaluationService::class);
    $provider = app(LlmManager::class)->provider('mock');

    $good = $evaluation->evaluate($provider, "You are a helper.\n- Say hello world", [$benchmark->load('cases.criteria')]);
    expect($good->score)->toBe(1.0);

    $bad = $evaluation->evaluate($provider, 'You are a helper.', [$benchmark->load('cases.criteria')]);
    expect($bad->score)->toBeLessThan(1.0);
});

it('honours not_contains criteria', function () {
    $benchmark = Benchmark::factory()->hasCasesWithCriteria([
        ['title' => 'No secrets', 'input' => 'Answer.', 'criteria' => [
            ['type' => 'not_contains', 'label' => 'Never say forbidden', 'config' => ['values' => ['forbidden']]],
        ]],
    ])->create();

    $evaluation = app(EvaluationService::class);
    $provider = app(LlmManager::class)->provider('mock');

    // Prompt does not instruct the word, but mock echoes requirements only,
    // so output should not contain it.
    $clean = $evaluation->evaluate($provider, 'Answer briefly.', [$benchmark->load('cases.criteria')]);
    expect($clean->score)->toBe(1.0);
});

it('validates regex patterns and rejects invalid ones', function () {
    $benchmark = Benchmark::factory()->hasCasesWithCriteria([
        ['title' => 'Regex', 'input' => 'Give a number.', 'criteria' => [
            ['type' => 'regex', 'label' => 'Contains digits', 'config' => ['pattern' => '/[0-9]/']],
        ]],
    ])->create();

    $evaluation = app(EvaluationService::class);
    $provider = app(LlmManager::class)->provider('mock');

    $withDigits = $evaluation->evaluate($provider, "- Return 42 as the answer.\nGive a number.", [$benchmark->load('cases.criteria')]);
    expect($withDigits->score)->toBe(1.0);

    $invalid = Benchmark::factory()->hasCasesWithCriteria([
        ['title' => 'Bad regex', 'input' => 'x', 'criteria' => [
            ['type' => 'regex', 'label' => 'Broken', 'config' => ['pattern' => '/[/']],
        ]],
    ])->create();

    $result = $evaluation->evaluate($provider, 'anything', [$invalid->load('cases.criteria')]);
    expect($result->score)->toBe(0.0);
});

it('evolves a prompt until the benchmark target is reached', function () {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->withContent('You are a marketing assistant.')->create();

    $benchmark = Benchmark::factory()->for($user)->hasCasesWithCriteria([
        ['title' => 'Tagline', 'input' => 'Write a tagline for an eco product.', 'criteria' => [
            ['type' => 'contains', 'label' => '- Include the phrase eco friendly', 'config' => ['values' => ['eco friendly']]],
            ['type' => 'contains', 'label' => '- Keep it under ten words', 'config' => ['values' => ['under ten words']]],
        ]],
    ])->create();

    $run = $user->runs()->create([
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'name' => 'Optimize tagline prompt',
        'mode' => 'optimize',
        'status' => 'pending',
        'provider' => 'mock',
        'max_steps' => 8,
        'target_score' => 0.95,
    ]);

    app(EvolutionService::class)->run($run, app(LlmManager::class)->provider('mock'));

    $run->refresh();

    expect($run->status)->toBe(RunStatus::Completed)
        ->and($run->steps()->whereNotNull('score')->max('score'))->toBeGreaterThanOrEqual(0.95);

    // The winning prompt must have been persisted as a new version.
    $prompt = $prompt->fresh();
    expect($prompt->currentVersion->version)->toBeGreaterThan(1)
        ->and($prompt->currentVersion->content)->toContain('eco friendly');
});

it('marks runs failed when no benchmarks are attached', function () {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->withContent('Test.')->create();

    $run = $user->runs()->create([
        'prompt_id' => $prompt->id,
        'name' => 'Empty run',
        'mode' => 'optimize',
        'status' => 'pending',
        'provider' => 'mock',
    ]);

    app(EvolutionService::class)->run($run, app(LlmManager::class)->provider('mock'));

    expect($run->fresh()->status)->toBe(RunStatus::Failed)
        ->and($run->error)->not->toBeNull();
});
