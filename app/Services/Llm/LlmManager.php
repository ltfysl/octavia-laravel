<?php

namespace App\Services\Llm;

use App\Models\ProviderKey;
use App\Models\User;
use App\Services\Llm\Contracts\LlmProvider;
use App\Services\Llm\Providers\MockLlmProvider;
use App\Services\Llm\Providers\OpenAiCompatibleProvider;
use InvalidArgumentException;
use RuntimeException;

class LlmManager
{
    public function provider(?string $name = null, ?User $user = null): LlmProvider
    {
        $name ??= config('llm.default');
        $config = config("llm.providers.{$name}");

        if ($config === null) {
            throw new InvalidArgumentException("Unknown LLM provider [{$name}].");
        }

        $apiKey = null;
        if ($user !== null && $config['driver'] === 'openai') {
            $providerKey = ProviderKey::forUser($user)->active()->where('provider', $name)->first();
            if ($providerKey !== null) {
                $apiKey = $providerKey->api_key;
                $providerKey->markUsed();
            }
        }

        return match ($config['driver']) {
            'mock' => new MockLlmProvider,
            'openai' => new OpenAiCompatibleProvider(
                apiKey: $apiKey ?? (string) $config['key'],
                baseUrl: (string) $config['base_url'],
                model: (string) $config['model'],
                timeout: (int) $config['timeout'],
            ),
            default => throw new InvalidArgumentException("Unknown LLM driver [{$config['driver']}]."),
        };
    }

    public function isConfigured(?string $name = null): bool
    {
        try {
            $this->provider($name);

            return true;
        } catch (InvalidArgumentException|RuntimeException) {
            return false;
        }
    }
}
