<?php

namespace App\Actions;

use App\Models\Benchmark;
use App\Models\Prompt;
use App\Services\EvaluationService;
use App\Services\Llm\Contracts\LlmProvider;
use Illuminate\Support\Collection;

/**
 * Ad-hoc regression test: run the current prompt version against a set of
 * benchmarks to verify it does not break existing cases. No Run records are
 * created, mirroring the ad-hoc Playground and A/B test paths.
 */
class RunPromptRegressionTest
{
    public function __construct(
        private readonly LlmProvider $provider,
        private readonly EvaluationService $evaluation,
    ) {}

    /**
     * @param  array<int>|null  $benchmarkIds
     * @return array{results: array<int, array<string, mixed>>, summary: array<string, mixed>}
     */
    public function __invoke(Prompt $prompt, ?array $benchmarkIds = null, ?string $sampleInput = null): array
    {
        $content = $prompt->currentContent() ?? '';

        $query = Benchmark::query()
            ->where(fn ($q) => $q->where('user_id', $prompt->user_id)->orWhere('visibility', 'public'))
            ->whereHas('cases')
            ->with('cases.criteria');

        if (! empty($benchmarkIds)) {
            $query->whereIn('id', $benchmarkIds);
        } else {
            $query->where('category', $prompt->category);
        }

        /** @var Collection<int, Benchmark> $benchmarks */
        $benchmarks = $query->get();

        if ($benchmarks->isEmpty()) {
            return [
                'results' => [],
                'summary' => [
                    'total' => 0,
                    'passed' => 0,
                    'failed' => 0,
                    'errors' => 0,
                    'avg_score' => 0.0,
                ],
            ];
        }

        // Optionally override every case input with the provided sample input.
        if ($sampleInput !== null) {
            $benchmarks->each(fn (Benchmark $b) => $b->cases->each(fn ($c) => $c->input = $sampleInput));
        }

        $summary = $this->evaluation->evaluate($this->provider, $content, $benchmarks);

        $results = [];
        $total = 0;
        $passed = 0;
        $failed = 0;

        foreach ($benchmarks as $benchmark) {
            $caseOutcomes = array_filter(
                $summary->cases,
                fn ($c) => $benchmark->cases->contains('id', $c->caseId)
            );

            $caseScores = array_map(fn ($c) => $c->score, $caseOutcomes);
            $benchmarkScore = $caseScores === [] ? 0.0 : array_sum($caseScores) / count($caseScores);
            $allPassed = count($caseOutcomes) > 0 && count(array_filter($caseOutcomes, fn ($c) => ! $c->passed)) === 0;

            $total += count($caseOutcomes);
            $passed += count(array_filter($caseOutcomes, fn ($c) => $c->passed));
            $failed += count($caseOutcomes) - count(array_filter($caseOutcomes, fn ($c) => $c->passed));

            $results[] = [
                'benchmark_id' => $benchmark->id,
                'benchmark_name' => $benchmark->name,
                'category' => $benchmark->category,
                'status' => $allPassed ? 'pass' : 'fail',
                'score' => $benchmarkScore,
                'cases' => array_map(fn ($c) => [
                    'case_id' => $c->caseId,
                    'title' => $c->title,
                    'score' => $c->score,
                    'passed' => $c->passed,
                    'output' => $c->output,
                ], $caseOutcomes),
            ];
        }

        $avgScore = $summary->score;

        return [
            'results' => $results,
            'summary' => [
                'total' => $total,
                'passed' => $passed,
                'failed' => $failed,
                'errors' => 0,
                'avg_score' => $avgScore,
            ],
        ];
    }
}
