<?php

namespace App\Models;

use App\Enums\RunMode;
use App\Enums\RunStatus;
use App\Observers\RunProgressObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([RunProgressObserver::class])]
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
        'best_score',
        'cost_optimized',
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
            'cost_optimized' => 'boolean',
        ];
    }

    public function usesCostOptimization(): bool
    {
        return $this->cost_optimized === true;
    }

    public function evaluationModel(): string
    {
        return $this->model ?? (string) config("llm.providers.{$this->provider}.model");
    }

    public function mutationModel(): string
    {
        if (! $this->usesCostOptimization()) {
            return $this->evaluationModel();
        }

        return (string) config('llm.cost_optimized.mutation_model');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Team-aware visibility: owner plus team mates sharing a team with
     * the owner — mirrors Prompt::scopeVisibleTo / Benchmark::scopeVisibleTo.
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $query->whereIn('user_id', $user->teamMateIds());
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
