<?php

namespace App\Enums;

enum RunMode: string
{
    case Evaluate = 'evaluate';
    case Optimize = 'optimize';
}
