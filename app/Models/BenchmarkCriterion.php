<?php

namespace App\Models;

use App\Enums\CriterionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BenchmarkCriterion extends Model
{
    /** @use HasFactory<BenchmarkCriterionFactory> */
    use HasFactory;

    protected $fillable = [
        'type',
        'label',
        'config',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'type' => CriterionType::class,
            'config' => 'array',
            'position' => 'integer',
        ];
    }

    public function benchmarkCase(): BelongsTo
    {
        return $this->belongsTo(BenchmarkCase::class, 'benchmark_case_id');
    }
}
