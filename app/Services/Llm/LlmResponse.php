<?php

namespace App\Services\Llm;

class LlmResponse
{
    public function __construct(
        public readonly string $content,
        public readonly int $promptTokens = 0,
        public readonly int $completionTokens = 0,
    ) {}

    public function totalTokens(): int
    {
        return $this->promptTokens + $this->completionTokens;
    }
}
