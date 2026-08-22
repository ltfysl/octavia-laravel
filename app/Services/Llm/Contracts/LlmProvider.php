<?php

namespace App\Services\Llm\Contracts;

use App\Services\Llm\LlmResponse;

interface LlmProvider
{
    /**
     * @param  list<array{role: string, content: string}>  $messages
     * @param  array{temperature?: float, max_tokens?: int, model?: string}  $options
     */
    public function complete(array $messages, array $options = []): LlmResponse;
}
