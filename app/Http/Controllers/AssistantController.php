<?php

namespace App\Http\Controllers;

use App\Services\Llm\Contracts\LlmProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AssistantController extends Controller
{
    /**
     * Stateless assistant chat. 10 req/min per user, offline via the
     * configured default provider (MockLlmProvider for tests).
     */
    public function __invoke(Request $request, LlmProvider $provider): JsonResponse
    {
        $validated = $request->validate([
            'messages' => ['required', 'array', 'max:20'],
            'messages.*.role' => ['required', Rule::in(['system', 'user', 'assistant'])],
            'messages.*.content' => ['required', 'string', 'max:4000'],
        ]);

        $messages = array_map(fn (array $m) => [
            'role' => $m['role'],
            'content' => $m['content'],
        ], $validated['messages']);

        $response = $provider->complete($messages, ['temperature' => 0.7, 'max_tokens' => 512]);

        return response()->json([
            'reply' => $response->content,
            'tokens' => $response->totalTokens(),
        ]);
    }
}
