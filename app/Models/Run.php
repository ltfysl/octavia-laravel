<?php

namespace App\Models;

use App\Enums\RunMode;
use App\Enums\RunStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Run extends Model
{
    protected $fillable = [
        'prompt_id',
        'benchmark_id',
        'collection_id',
        'name',
        'mode',
        'status',
        'provider',
        'model',
        'max_steps',
        'target_score',
    ];

    protected function casts(): array
    {
        return [
            'mode' => RunMode::class,
            'status' => RunStatus::class,
            'target_score' => 'float',
            'best_score' => 'float',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function prompt(): BelongsTo
    {
        return $this->belongsTo(Prompt::class);
    }

    public function benchmark(): BelongsTo
    {
        return $this->belongsTo(Benchmark::class);
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(BenchmarkCollection::class, 'collection_id');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(RunStep::class)->orderBy('number');
    }

    public function bestStep(): ?RunStep
    {
        return $this->steps()->orderByDesc('score')->first();
    }

    public function benchmarks(): array
    {
        if ($this->benchmark_id) {
            return [$this->benchmark];
        }

        return $this->collection?->benchmarks()->with('cases.criteria')->get()->all() ?? [];
    }

    public function isFinished(): bool
    {
        return in_array($this->status, [RunStatus::Completed, RunStatus::Failed, RunStatus::Cancelled], true);
    }
}
