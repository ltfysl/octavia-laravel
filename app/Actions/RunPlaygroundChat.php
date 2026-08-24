<?php

namespace App\Actions;

use App\Models\Prompt;
use App\Services\Llm\Contracts\LlmProvider;

/**
 * Multi-turn playground for a prompt: the prompt content becomes the
 * system message, all user-provided messages are sent to the model.
 * No persistence — this is a quick preview before committing a version.
 */
class RunPlaygroundChat
{
    public function __construct(private readonly LlmProvider $provider) {}

    /**
     * @param  array<int, array{role: string, content: string}>  $messages
     * @return array{output: string}
     */
    public function __invoke(Prompt $prompt, array $messages, ?string $contentOverride = null): array
    {
        $content = $contentOverride ?? $prompt->currentContent() ?? '';

        $history = array_filter($messages, fn ($m) => in_array($m['role'] ?? '', ['user', 'assistant'], true));

        $response = $this->provider->complete([
            ['role' => 'system', 'content' => $content],
            ...$history,
        ]);

        return ['output' => $response->content];
    }
}
