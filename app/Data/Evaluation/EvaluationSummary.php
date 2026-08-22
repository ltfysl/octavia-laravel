<?php

namespace App\Data\Evaluation;

readonly class EvaluationSummary
{
    /**
     * @param  list<CaseOutcome>  $cases
     */
    public function __construct(
        public float $score,
        public array $cases,
        public int $tokensUsed = 0,
    ) {}

    /** @return list<CaseOutcome> */
    public function failingCases(): array
    {
        return array_values(array_filter(
            $this->cases,
            fn (CaseOutcome $case) => ! $case->passed,
        ));
    }

    public function toArray(): array
    {
        return [
            'score' => round($this->score, 3),
            'tokens_used' => $this->tokensUsed,
            'cases' => array_map(fn (CaseOutcome $case) => $case->toArray(), $this->cases),
        ];
    }
}
