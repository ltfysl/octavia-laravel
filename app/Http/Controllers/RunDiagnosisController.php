<?php

namespace App\Http\Controllers;

use App\Models\Run;
use App\Services\Llm\Contracts\LlmProvider;
use Illuminate\Http\JsonResponse;

class RunDiagnosisController extends Controller
{
    /**
     * AI diagnosis for a failed or cancelled run. Owner-only.
     */
    public function __invoke(Run $run, LlmProvider $provider): JsonResponse
    {
        abort_unless($run->user_id === auth()->id(), 404);

        if (! in_array($run->status->value, ['failed', 'cancelled'], true)) {
            return response()->json(['diagnosis' => 'Diagnosis is only available for failed or cancelled runs.']);
        }

        $run->load(['steps', 'benchmark', 'prompt']);

        $steps = $run->steps->map(fn ($step) => [
            'number' => $step->number,
            'phase' => $step->phase->value,
            'status' => $step->status->value,
            'score' => $step->score,
            'output' => $step->output,
            'error' => $step->error,
        ])->all();

        $summary = [
            "Run: {$run->name}",
            "Status: {$run->status->value}",
            "Mode: {$run->mode->value}",
            'Prompt: '.($run->prompt?->name ?? '—'),
            'Benchmark: '.($run->benchmark?->name ?? '—'),
            'Best score: '.($run->best_score ?? '—'),
            'Steps:',
            ...array_map(fn ($s) => "- Step {$s['number']} ({$s['phase']}, {$s['status']}) score={$s['score']} output={$s['output']} error={$s['error']}", $steps),
        ];

        $messages = [
            ['role' => 'system', 'content' => '[OCTAVIA-DIAGNOSIS] You diagnose failed or cancelled prompt-lab runs. Answer with one likely cause and one concrete next step. Be brief.'],
            ['role' => 'user', 'content' => implode("\n", $summary)],
        ];

        $response = $provider->complete($messages, ['temperature' => 0.3, 'max_tokens' => 400]);

        return response()->json([
            'diagnosis' => $response->content,
            'tokens' => $response->totalTokens(),
        ]);
    }
}
