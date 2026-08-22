<?php

namespace App\Actions;

use App\Models\Prompt;
use App\Services\Llm\Contracts\LlmProvider;

/**
 * Runs a prompt against a single ad-hoc user input without creating a run.
 * Used by the prompt playground; supports testing unsaved content edits.
 */
class RunPlayground
{
    public function __construct(private readonly LlmProvider $provider) {}

    /**
     * @return array{output: string}
     */
    public function __invoke(Prompt $prompt, string $input, ?string $contentOverride = null): array
    {
        $content = $contentOverride ?? $prompt->currentContent() ?? '';

        $response = $this->provider->complete([
            ['role' => 'system', 'content' => $content],
            ['role' => 'user', 'content' => $input],
        ]);

        return ['output' => $response->content];
    }
}
