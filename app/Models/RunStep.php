<?php

namespace App\Models;

use App\Enums\StepPhase;
use App\Observers\RunStepProgressObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([RunStepProgressObserver::class])]
class RunStep extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'run_id',
        'number',
        'phase',
        'prompt_content',
        'score',
        'mutation_type',
        'rationale',
        'tokens_used',
    ];

    protected function casts(): array
    {
        return [
            'phase' => StepPhase::class,
            'score' => 'float',
            'number' => 'integer',
            'tokens_used' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function run(): BelongsTo
    {
        return $this->belongsTo(Run::class);
    }

    public function caseResults(): HasMany
    {
        return $this->hasMany(CaseResult::class);
    }
}
