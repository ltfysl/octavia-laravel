<?php

namespace App\Data\Evaluation;

readonly class CaseOutcome
{
    /**
     * @param  list<CriterionOutcome>  $criteria
     */
    public function __construct(
        public int $caseId,
        public string $title,
        public float $weight,
        public float $score,
        public bool $passed,
        public ?string $output,
        public array $criteria,
    ) {}

    public function toArray(): array
    {
        return [
            'case_id' => $this->caseId,
            'title' => $this->title,
            'weight' => $this->weight,
            'score' => round($this->score, 3),
            'passed' => $this->passed,
            'output' => $this->output,
            'criteria' => array_map(fn (CriterionOutcome $c) => $c->toArray(), $this->criteria),
        ];
    }
}
