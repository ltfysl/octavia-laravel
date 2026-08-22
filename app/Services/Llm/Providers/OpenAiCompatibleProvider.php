<?php

namespace App\Services\Llm\Providers;

use App\Services\Llm\Contracts\LlmProvider;
use App\Services\Llm\LlmResponse;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Talks to any OpenAI-compatible chat-completions endpoint
 * (OpenAI, Azure, OpenRouter, Ollama, LM Studio, ...).
 */
class OpenAiCompatibleProvider implements LlmProvider
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $baseUrl,
        private readonly string $model,
        private readonly int $timeout = 60,
    ) {
        if (trim($this->apiKey) === '') {
            throw new RuntimeException('OpenAI-compatible provider requires an API key.');
        }
    }

    public function complete(array $messages, array $options = []): LlmResponse
    {
        $payload = [
            'model' => $options['model'] ?? $this->model,
            'messages' => $messages,
            'temperature' => $options['temperature'] ?? 0.7,
        ];

        if (isset($options['max_tokens'])) {
            $payload['max_tokens'] = $options['max_tokens'];
        }

        try {
            $response = Http::baseUrl($this->baseUrl)
                ->withToken($this->apiKey)
                ->timeout($this->timeout)
                ->retry(2, 500, throw: false)
                ->post('/chat/completions', $payload);
        } catch (ConnectionException $e) {
            throw new RuntimeException('LLM request timed out: '.$e->getMessage(), previous: $e);
        }

        if ($response->failed()) {
            throw new RuntimeException(sprintf(
                'LLM request failed [%d]: %s',
                $response->status(),
                mb_substr($response->body(), 0, 500),
            ));
        }

        $data = $response->json();

        return new LlmResponse(
            content: data_get($data, 'choices.0.message.content', ''),
            promptTokens: (int) data_get($data, 'usage.prompt_tokens', 0),
            completionTokens: (int) data_get($data, 'usage.completion_tokens', 0),
        );
    }
}
