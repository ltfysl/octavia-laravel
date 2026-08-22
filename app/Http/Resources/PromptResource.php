<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'visibility' => $this->visibility->value,
            'version' => $this->whenLoaded('currentVersion', fn () => $this->currentVersion?->version),
            'content' => $this->when(
                $request->routeIs('api.prompts.show'),
                fn () => $this->currentVersion?->content,
            ),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
