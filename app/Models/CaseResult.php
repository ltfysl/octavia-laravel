<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CaseResult extends Model
{
    protected $fillable = [
        'benchmark_case_id',
        'score',
        'passed',
        'output',
        'feedback',
    ];

    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'score' => 'float',
            'passed' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function runStep(): BelongsTo
    {
        return $this->belongsTo(RunStep::class, 'run_step_id');
    }

    public function benchmarkCase(): BelongsTo
    {
        return $this->belongsTo(BenchmarkCase::class, 'benchmark_case_id');
    }

    public function criteriaResults(): HasMany
    {
        return $this->hasMany(CriterionResult::class);
    }
}
