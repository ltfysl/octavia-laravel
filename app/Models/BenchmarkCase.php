<?php

namespace App\Models;

use Database\Factories\BenchmarkCaseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BenchmarkCase extends Model
{
    /** @use HasFactory<BenchmarkCaseFactory> */
    use HasFactory;

    protected $table = 'benchmark_cases';

    protected $fillable = [
        'title',
        'input',
        'weight',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'float',
            'position' => 'integer',
        ];
    }

    public function benchmark(): BelongsTo
    {
        return $this->belongsTo(Benchmark::class, 'benchmark_id');
    }

    public function criteria(): HasMany
    {
        return $this->hasMany(BenchmarkCriterion::class)->orderBy('position');
    }
}
