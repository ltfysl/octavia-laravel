<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CriterionResult extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'criterion_label',
        'passed',
        'detail',
    ];

    protected function casts(): array
    {
        return [
            'passed' => 'boolean',
            'detail' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function caseResult(): BelongsTo
    {
        return $this->belongsTo(CaseResult::class, 'case_result_id');
    }
}
