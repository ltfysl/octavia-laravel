<?php

namespace App\Jobs;

use App\Enums\RunMode;
use App\Enums\RunStatus;
use App\Enums\StepPhase;
use App\Models\Run;
use App\Models\RunStep;
use App\Services\EvaluationService;
use App\Services\EvolutionService;
use App\Services\Llm\Contracts\LlmProvider;
use App\Services\Llm\LlmManager;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class ProcessRunJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function __construct(public readonly int $runId) {}

    public function handle(LlmManager $manager, EvolutionService $evolution, EvaluationService $evaluation): void
    {
        $run = Run::with([
            'prompt',
            'benchmark.cases.criteria',
            'collection.benchmarks.cases.criteria',
        ])->find($this->runId);

        if (! $run || $run->isFinished()) {
            return;
        }

        try {
            $provider = $manager->provider($run->provider, $run->user);
        } catch (Throwable $e) {
            $this->fail($run, $e->getMessage());

            return;
        }
        if ($run->mode === RunMode::Evaluate) {
            $this->evaluateOnce($run, $evaluation, $provider);
        } elseif ($run->mode === RunMode::Regression) {
            $this->regressionTest($run, $evaluation, $provider);
        } else {
            $evolution->run($run, $provider);
        }
    }

    private function evaluateOnce(Run $run, EvaluationService $evaluation, LlmProvider $provider): void
    {
        $benchmarks = $run->benchmarks();

        if ($benchmarks === []) {
            $this->fail($run, 'Run has no benchmarks.');

            return;
        }

        $run->update(['status' => RunStatus::Running, 'started_at' => now()]);

        $content = $run->prompt->currentContent() ?? '';
        $summary = $evaluation->evaluate($provider, $content, $benchmarks, ['model' => $run->evaluationModel()]);

        $step = $run->steps()->create([
            'number' => 1,
            'phase' => StepPhase::Evaluate,
            'prompt_content' => $content,
            'score' => $summary->score,
            'mutation_type' => 'initial',
            'tokens_used' => $summary->tokensUsed,
            'created_at' => now(),
        ]);

        foreach ($summary->cases as $caseOutcome) {
            $caseResult = $step->caseResults()->create([
                'benchmark_case_id' => $caseOutcome->caseId,
                'score' => $caseOutcome->score,
                'passed' => $caseOutcome->passed,
                'output' => $caseOutcome->output,
                'created_at' => now(),
            ]);

            foreach ($caseOutcome->criteria as $criterion) {
                $caseResult->criteriaResults()->create([
                    'criterion_id' => $criterion->criterionId,
                    'criterion_label' => $criterion->label,
                    'passed' => $criterion->passed,
                    'detail' => $criterion->detail,
                    'created_at' => now(),
                ]);
            }
        }

        $run->forceFill([
            'status' => RunStatus::Completed,
            'best_score' => $summary->score,
            'finished_at' => now(),
        ])->save();
    }

    private function regressionTest(Run $run, EvaluationService $evaluation, LlmProvider $provider): void
    {
        $benchmarks = $run->benchmarks();

        if ($benchmarks === []) {
            $this->fail($run, 'Run has no benchmarks.');

            return;
        }

        $run->update(['status' => RunStatus::Running, 'started_at' => now()]);

        $content = $run->prompt->currentContent() ?? '';
        $summary = $evaluation->evaluate($provider, $content, $benchmarks, ['model' => $run->evaluationModel()]);

        $step = $run->steps()->create([
            'number' => 1,
            'phase' => StepPhase::Evaluate,
            'prompt_content' => $content,
            'score' => $summary->score,
            'mutation_type' => 'initial',
            'tokens_used' => $summary->tokensUsed,
            'created_at' => now(),
        ]);

        foreach ($summary->cases as $caseOutcome) {
            $caseResult = $step->caseResults()->create([
                'benchmark_case_id' => $caseOutcome->caseId,
                'score' => $caseOutcome->score,
                'passed' => $caseOutcome->passed,
                'output' => $caseOutcome->output,
                'created_at' => now(),
            ]);

            foreach ($caseOutcome->criteria as $criterion) {
                $caseResult->criteriaResults()->create([
                    'criterion_id' => $criterion->criterionId,
                    'criterion_label' => $criterion->label,
                    'passed' => $criterion->passed,
                    'detail' => $criterion->detail,
                    'created_at' => now(),
                ]);
            }
        }

        $report = $this->buildRegressionReport($run, $step);

        $run->forceFill([
            'status' => RunStatus::Completed,
            'best_score' => $summary->score,
            'regression_report' => $report,
            'finished_at' => now(),
        ])->save();
    }

    /**
     * @return array<string, mixed>|null
     */
    private function buildRegressionReport(Run $run, RunStep $step): ?array
    {
        $benchmarkId = $run->benchmark_id;
        $collectionId = $run->collection_id;

        $baseline = Run::query()
            ->where('id', '!=', $run->id)
            ->where('prompt_id', $run->prompt_id)
            ->where('status', RunStatus::Completed)
            ->where(fn ($q) => $q->where('benchmark_id', $benchmarkId)->orWhere('collection_id', $collectionId))
            ->whereIn('mode', [RunMode::Evaluate, RunMode::Optimize])
            ->orderByDesc('created_at')
            ->first();

        if (! $baseline) {
            return null;
        }

        $baselineStep = $baseline->bestStep() ?? $baseline->steps()->orderByDesc('score')->first();
        if (! $baselineStep) {
            return null;
        }

        $baselineScores = $baselineStep->caseResults()->pluck('score', 'benchmark_case_id')->toArray();
        $current = $step->caseResults()->get()->map(fn ($cr) => [
            'case_id' => $cr->benchmark_case_id,
            'score' => $cr->score,
            'passed' => $cr->passed,
        ])->toArray();

        $deltas = [];
        $improved = 0;
        $degraded = 0;
        $unchanged = 0;

        foreach ($current as $c) {
            $caseId = $c['case_id'];
            $previous = $baselineScores[$caseId] ?? null;
            $delta = $previous === null ? null : round($c['score'] - $previous, 3);
            $deltas[] = [
                'case_id' => $caseId,
                'current' => $c['score'],
                'previous' => $previous,
                'delta' => $delta,
            ];

            if ($delta === null) {
                continue;
            }

            if ($delta > 0.001) {
                $improved++;
            } elseif ($delta < -0.001) {
                $degraded++;
            } else {
                $unchanged++;
            }
        }

        return [
            'baseline_run_id' => $baseline->id,
            'baseline_run_name' => $baseline->name,
            'baseline_score' => $baselineStep->score,
            'current_score' => $step->score,
            'score_delta' => round($step->score - $baselineStep->score, 3),
            'improved_cases' => $improved,
            'degraded_cases' => $degraded,
            'unchanged_cases' => $unchanged,
            'deltas' => $deltas,
        ];
    }

    private function fail(Run $run, string $message): void
    {
        $run->forceFill([
            'status' => RunStatus::Failed,
            'error' => mb_substr($message, 0, 2000),
            'finished_at' => now(),
        ])->save();
    }

    public function failed(Throwable $exception): void
    {
        $run = Run::find($this->runId);

        if ($run && ! $run->isFinished()) {
            $run->forceFill([
                'status' => RunStatus::Failed,
                'error' => mb_substr($exception->getMessage(), 0, 2000),
                'finished_at' => now(),
            ])->save();
        }
    }
}
