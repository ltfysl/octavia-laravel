<?php

namespace App\Jobs;

use App\Enums\RunMode;
use App\Enums\RunStatus;
use App\Enums\StepPhase;
use App\Models\Run;
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
        $summary = $evaluation->evaluate($provider, $content, $benchmarks);

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

    private function fail(Run $run, string $message): void
    {
        $run->forceFill([
            'status' => RunStatus::Failed,
            'error' => mb_substr($message, 0, 2000),
            'finished_at' => now(),
        ])->save();
    }
}
