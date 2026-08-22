<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BenchmarkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category->value,
            'visibility' => $this->visibility->value,
            'version' => $this->version,
            'cases' => $this->whenLoaded('cases', fn () => $this->cases->map(fn ($case) => [
                'id' => $case->id,
                'title' => $case->title,
                'input' => $case->input,
                'weight' => (float) $case->weight,
                'criteria' => $case->criteria->map(fn ($c) => [
                    'id' => $c->id,
                    'type' => $c->type->value,
                    'label' => $c->label,
                    'config' => $c->config,
                ]),
            ])),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
