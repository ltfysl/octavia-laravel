<?php

namespace App\Enums;

enum CriterionType: string
{
    case Contains = 'contains';
    case NotContains = 'not_contains';
    case Regex = 'regex';
    case LlmJudge = 'llm_judge';

    public function isProgrammatic(): bool
    {
        return ! in_array($this, [self::LlmJudge], true);
    }
}
