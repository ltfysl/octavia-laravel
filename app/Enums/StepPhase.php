<?php

namespace App\Enums;

enum StepPhase: string
{
    case Evaluate = 'evaluate';
    case Mutate = 'mutate';
}
