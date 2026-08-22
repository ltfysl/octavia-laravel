<?php

namespace App\Enums;

enum MarketplaceItemType: string
{
    case Prompt = 'prompt';
    case Benchmark = 'benchmark';
}
