<?php

namespace App\Data\Evaluation;

readonly class CriterionOutcome
{
    public function __construct(
        public ?int $criterionId,
        public string $label,
        public string $type,
        public bool $passed,
        public float $score,
        public array $detail = [],
    ) {}

    public function toArray(): array
    {
        return [
            'criterion_id' => $this->criterionId,
            'label' => $this->label,
            'type' => $this->type,
            'passed' => $this->passed,
            'score' => round($this->score, 3),
            'detail' => $this->detail,
        ];
    }
}
