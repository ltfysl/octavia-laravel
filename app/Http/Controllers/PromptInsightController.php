<?php

namespace App\Http\Controllers;

use App\Models\Prompt;
use App\Services\Llm\Contracts\LlmProvider;
use Illuminate\Http\JsonResponse;

class PromptInsightController extends Controller
{
    /**
     * On-demand AI review of the current prompt version: structure,
     * clarity and coverage feedback. Stateless, no persistence.
     */
    public function __invoke(Prompt $prompt, LlmProvider $provider): JsonResponse
    {
        abort_unless($prompt->user_id === auth()->id(), 404);

        $content = (string) $prompt->currentVersion?->content;
        abort_if($content === '', 422, 'Prompt has no content.');

        $messages = [
            ['role' => 'system', 'content' => '[OCTAVIA-INSIGHT] You review prompts for a prompt-engineering lab. Answer with 3-5 short bullet points covering clarity, structure and measurable criteria. No preamble.'],
            ['role' => 'user', 'content' => "Review this prompt:\n\n---\n{$content}\n---"],
        ];

        $response = $provider->complete($messages, ['temperature' => 0.4, 'max_tokens' => 400]);

        return response()->json([
            'insight' => $response->content,
            'tokens' => $response->totalTokens(),
        ]);
    }
}
