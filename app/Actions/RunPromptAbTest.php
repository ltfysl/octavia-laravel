<?php

namespace App\Actions;

use App\Models\Benchmark;
use App\Models\PromptVersion;
use App\Services\EvaluationService;
use App\Services\Llm\Contracts\LlmProvider;

class RunPromptAbTest
{
    public function __construct(
        private EvaluationService $evaluation,
        private LlmProvider $provider,
    ) {}

    /**
     * Evaluate two prompt versions against the same benchmark and return a comparison.
     *
     * @return array{version_a: array{version: int, score: float, tokens: int}, version_b: array{version: int, score: float, tokens: int}, winner: string, benchmark: string}
     */
    public function __invoke(PromptVersion $versionA, PromptVersion $versionB, Benchmark $benchmark): array
    {
        $benchmark->load('cases.criteria');

        $summaryA = $this->evaluation->evaluate($this->provider, $versionA->content, [$benchmark]);
        $summaryB = $this->evaluation->evaluate($this->provider, $versionB->content, [$benchmark]);

        $winner = match (true) {
            $summaryA->score > $summaryB->score => 'a',
            $summaryB->score > $summaryA->score => 'b',
            default => 'tie',
        };

        return [
            'version_a' => [
                'version' => $versionA->version,
                'score' => $summaryA->score,
                'tokens' => $summaryA->tokensUsed,
            ],
            'version_b' => [
                'version' => $versionB->version,
                'score' => $summaryB->score,
                'tokens' => $summaryB->tokensUsed,
            ],
            'winner' => $winner,
            'benchmark' => $benchmark->name,
        ];
    }
}
