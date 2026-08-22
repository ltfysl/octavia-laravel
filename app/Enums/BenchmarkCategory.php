<?php

namespace App\Enums;

enum BenchmarkCategory: string
{
    case Coding = 'coding';
    case Marketing = 'marketing';
    case Sales = 'sales';
    case Writing = 'writing';
    case Support = 'support';
    case Analysis = 'analysis';
    case General = 'general';

    public function label(): string
    {
        return __("benchmarks.categories.{$this->value}");
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
