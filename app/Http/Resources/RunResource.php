<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RunResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status->value,
            'mode' => $this->mode->value,
            'best_score' => $this->best_score,
            'target_score' => $this->target_score,
            'provider' => $this->provider,
            'error' => $this->error,
            'prompt' => $this->whenLoaded('prompt', fn () => $this->prompt?->only(['id', 'name'])),
            'benchmark' => $this->whenLoaded('benchmark', fn () => $this->benchmark?->only(['id', 'name'])),
            'regression_report' => $this->regression_report,
            'steps' => $this->whenLoaded('steps', fn () => $this->steps->map(fn ($step) => [
                'number' => $step->number,
                'phase' => $step->phase->value,
                'score' => $step->score,
                'mutation_type' => $step->mutation_type,
                'rationale' => $step->rationale,
                'prompt_content' => $step->prompt_content,
                'tokens_used' => $step->tokens_used,
            ])),
            'created_at' => $this->created_at?->toIso8601String(),
            'finished_at' => $this->finished_at?->toIso8601String(),
        ];
    }
}
