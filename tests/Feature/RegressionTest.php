<?php

use App\Enums\RunMode;
use App\Enums\RunStatus;
use App\Jobs\ProcessRunJob;
use App\Models\Benchmark;
use App\Models\BenchmarkCase;
use App\Models\BenchmarkCriterion;
use App\Models\Prompt;
use App\Models\Run;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('regression run compares against a baseline run', function () {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->recycle($user)->create();
    $benchmark = Benchmark::factory()->recycle($user)->create();
    $cases = BenchmarkCase::factory()->recycle($user)->count(2)->create(['benchmark_id' => $benchmark->id]);
    foreach ($cases as $case) {
        BenchmarkCriterion::factory()->recycle($user)->create(['benchmark_case_id' => $case->id]);
    }

    $baseline = Run::factory()->recycle($user)->create([
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'mode' => RunMode::Evaluate,
        'status' => RunStatus::Pending,
        'provider' => 'mock',
    ]);

    ProcessRunJob::dispatchSync($baseline->id);

    $regression = Run::factory()->recycle($user)->create([
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'mode' => RunMode::Regression,
        'status' => RunStatus::Pending,
        'provider' => 'mock',
    ]);

    ProcessRunJob::dispatchSync($regression->id);

    $regression->refresh();

    expect($regression->status)->toBe(RunStatus::Completed)
        ->and($regression->regression_report)->toBeArray()
        ->and($regression->regression_report['baseline_run_id'])->toBe($baseline->id)
        ->and($regression->regression_report)->toHaveKey('deltas')
        ->and($regression->regression_report['improved_cases'] + $regression->regression_report['degraded_cases'] + $regression->regression_report['unchanged_cases'])->toBe(2);
});
