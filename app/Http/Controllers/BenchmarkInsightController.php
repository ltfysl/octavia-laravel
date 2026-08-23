<?php

namespace App\Http\Controllers;

use App\Models\Benchmark;
use App\Services\Llm\Contracts\LlmProvider;
use Illuminate\Http\JsonResponse;

class BenchmarkInsightController extends Controller
{
    public function __invoke(Benchmark $benchmark, LlmProvider $provider): JsonResponse
    {
        abort_unless($benchmark->user_id === auth()->id(), 404);

        $benchmark->load('cases.criteria');

        if ($benchmark->cases->isEmpty()) {
            return response()->json(['insight' => 'No cases yet — add at least one case before reviewing coverage.', 'tokens' => 0]);
        }

        $summary = [
            'Benchmark: '.$benchmark->name,
            'Description: '.($benchmark->description ?? '—'),
            'Cases: '.$benchmark->cases->count(),
            ...$benchmark->cases->map(fn ($case) => '- '.($case->title ?? 'Untitled').': input="'.($case->input ?? '').'" criteria='.($case->criteria->pluck('label')->implode(', ') ?: 'none'))->all(),
        ];

        $messages = [
            ['role' => 'system', 'content' => '[OCTAVIA-INSIGHT] You review benchmark suites for a prompt-engineering lab. Answer with 3-5 short bullet points covering variety, measurability and coverage. No preamble.'],
            ['role' => 'user', 'content' => "Review this benchmark suite:\n\n---\n".implode("\n", $summary)."\n---"],
        ];

        $response = $provider->complete($messages, ['temperature' => 0.4, 'max_tokens' => 400]);

        return response()->json([
            'insight' => $response->content,
            'tokens' => $response->totalTokens(),
        ]);
    }
}
