<?php

namespace App\Services;

use App\Models\PromptVersion;
use App\Services\Llm\Contracts\LlmProvider;

class DiffExplainService
{
    public function __construct(private readonly LlmProvider $provider) {}

    public function explain(PromptVersion $from, PromptVersion $to): array
    {
        $system = <<<'PROMPT'
You are an expert prompt engineer. Analyze the differences between two versions of a prompt.
Return only a JSON object with this shape:
{
  "summary": "One-sentence overview of the change",
  "changes": [
    { "type": "major|minor|formatting", "description": "What changed", "impact": "positive|negative|neutral" }
  ],
  "recommendation": "One concrete next step for the user"
}
Focus on actionable insights. Do not include markdown code fences.
[OCTAVIA-DIFF-EXPLAIN]
PROMPT;

        $user = "---FROM---\n{$from->content}\n---TO---\n{$to->content}";

        $response = $this->provider->complete([
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => $user],
        ]);

        $json = $this->extractJson($response->content);

        try {
            $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            $decoded = null;
        }

        if (! is_array($decoded)) {
            return [
                'summary' => 'The model did not return a structured explanation.',
                'changes' => [],
                'recommendation' => 'Evaluate the new version against a benchmark.',
            ];
        }

        return [
            'summary' => $decoded['summary'] ?? 'No summary generated.',
            'changes' => $decoded['changes'] ?? [],
            'recommendation' => $decoded['recommendation'] ?? 'Evaluate the new version against a benchmark.',
        ];
    }

    private function extractJson(string $content): string
    {
        $content = trim($content);
        if (str_starts_with($content, '```')) {
            $content = preg_replace('/^```(?:json)?\s*\n?/', '', $content) ?? $content;
            $content = preg_replace('/\n?```\s*$/', '', $content) ?? $content;
        }

        return trim($content);
    }
}
