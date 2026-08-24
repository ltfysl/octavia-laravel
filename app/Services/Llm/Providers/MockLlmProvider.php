<?php

namespace App\Services\Llm\Providers;

use App\Services\Llm\Contracts\LlmProvider;
use App\Services\Llm\LlmResponse;
use Illuminate\Support\Str;

/**
 * Deterministic, credential-free provider used for local development,
 * demos, CI and tests.
 *
 * Five stable, testable modes keyed off system-prompt markers:
 *
 * 1. Judge mode (`[OCTAVIA-JUDGE]`): scores how many meaningful words of
 *    the stated requirement appear in the model output. Deterministic.
 * 2. Optimizer mode (`[OCTAVIA-OPTIMIZER]`): returns an improved prompt by
 *    appending every unmet requirement it can find as an explicit bullet —
 *    which, combined with task-mode echo behaviour, lets the evolution
 *    engine demonstrably climb without any external API.
 * 3. Insight mode (`[OCTAVIA-INSIGHT]`): returns a short structured review
 *    of a prompt or benchmark — structure score, clarity, measurability
 *    and coverage. Used by the assistant and prompt-insight endpoints.
 * 4. Diagnosis mode (`[OCTAVIA-DIAGNOSIS]`): summarises the run context
 *    and returns a likely cause plus one concrete next step. Used for
 *    failed or cancelled run detail pages.
 * 5. Diff-explain mode (`[OCTAVIA-DIFF-EXPLAIN]`): explains changes between
 *    two prompt versions as structured JSON.
 * 6. Task mode (default): echoes the user input and repeats every explicit
 *    bullet / numbered requirement found in the system prompt.
 */
class MockLlmProvider implements LlmProvider
{
    public function complete(array $messages, array $options = []): LlmResponse
    {
        $system = '';
        $user = '';

        foreach ($messages as $message) {
            if (($message['role'] ?? '') === 'system') {
                $system .= "\n".$message['content'];
            } else {
                $user .= "\n".$message['content'];
            }
        }

        return match (true) {
            Str::contains($system, '[OCTAVIA-JUDGE]') => $this->judge($user),
            Str::contains($system, '[OCTAVIA-OPTIMIZER]') => $this->optimize($user),
            Str::contains($system, '[OCTAVIA-INSIGHT]') => $this->insight($user),
            Str::contains($system, '[OCTAVIA-DIAGNOSIS]') => $this->diagnosis($user),
            Str::contains($system, '[OCTAVIA-DIFF-EXPLAIN]') => $this->diffExplain($user),
            default => $this->task($system, $user),
        };
    }

    /**
     * Prompt-review branch for the [OCTAVIA-INSIGHT] system marker.
     * Deterministic: reports structure score and requirement coverage.
     */
    private function insight(string $user): LlmResponse
    {
        $lines = [
            '- Structure: '.($this->structureScore($user) >= 1 ? 'good — the prompt uses explicit sections' : 'add explicit sections (role, task, requirements)'),
            '- Clarity: keep instructions short and imperative; avoid nested conditionals',
            '- Measurability: '.(count($this->extractRequirements($user)) >= 1 ? 'requirements detected — map each to a benchmark case' : 'no bullet requirements found — add 2-3 measurable criteria'),
            '- Coverage: add one adversarial case (empty input, very long input) to your benchmark',
        ];

        return new LlmResponse(implode("\n", $lines), Str::wordCount($user), Str::wordCount(implode("\n", $lines)));
    }

    /**
     * Run-diagnosis branch for the [OCTAVIA-DIAGNOSIS] system marker.
     * Deterministic: picks a cause from the run context and recommends
     * one concrete next step.
     */
    private function diagnosis(string $user): LlmResponse
    {
        $context = mb_strtolower($user);

        if (str_contains($context, 'cancelled')) {
            $cause = 'The run was cancelled manually.';
            $next = 'If that was unintentional, start a new run with the same prompt and benchmark.';
        } elseif (str_contains($context, 'timeout') || str_contains($context, 'took too long')) {
            $cause = 'A step exceeded the configured timeout.';
            $next = 'Try reducing max_steps or switching to a faster provider/model in Settings.';
        } elseif (str_contains($context, 'no benchmarks') || str_contains($context, 'empty')) {
            $cause = 'The run had no benchmark cases to evaluate against.';
            $next = 'Add at least one test case to the benchmark before running again.';
        } elseif (count($this->extractRequirements($context)) >= 1) {
            $cause = 'Evaluation passed partial checks but did not reach the target score.';
            $next = 'Use the Optimize mode to evolve the prompt against these requirements.';
        } else {
            $cause = 'An unexpected error occurred during execution.';
            $next = 'Check the application logs and re-run with the same inputs.';
        }

        $text = "- Likely cause: {$cause}\n- Next step: {$next}";

        return new LlmResponse($text, Str::wordCount($user), Str::wordCount($text));
    }

    private function judge(string $user): LlmResponse
    {
        preg_match('/Requirement:\s*(.+)/', $user, $reqMatch);
        preg_match('/---\s*(.*?)\s*---/s', $user, $outMatch);

        $requirement = trim($reqMatch[1] ?? '');
        $output = mb_strtolower(trim($outMatch[1] ?? ''));

        $keywords = array_values(array_filter(
            preg_split('/\W+/u', mb_strtolower($requirement)) ?: [],
            fn (string $w) => mb_strlen($w) >= 4 && ! is_numeric($w),
        ));

        $hit = 0;
        foreach ($keywords as $keyword) {
            if (Str::contains($output, $keyword)) {
                $hit++;
            }
        }

        $score = $keywords === [] ? 1.0 : round($hit / count($keywords), 2);

        return new LlmResponse("Analysis complete.\nSCORE: {$score}", 10, 5);
    }

    private function optimize(string $user): LlmResponse
    {
        preg_match('/<PROMPT>\s*(.*?)\s*<\/PROMPT>/s', $user, $promptMatch);
        $prompt = trim($promptMatch[1] ?? '');

        preg_match_all('/^-\s+(.+)$/m', mb_strtolower($user), $unmet);
        $existing = mb_strtolower($prompt);

        $additions = [];
        foreach (array_unique($unmet[1] ?? []) as $line) {
            $line = trim(preg_replace('/^(?:unmet|failed|missing)\s*:\s*/i', '', $line) ?? $line);

            if ($line !== '' && ! Str::contains($existing, mb_strtolower($line))) {
                $additions[] = '- '.$line;
            }
        }

        $improved = $prompt.($additions === [] ? '' : "\n\nRequirements:\n".implode("\n", $additions));

        return new LlmResponse(
            "Rationale: added explicit requirements that were not satisfied.\n<PROMPT>\n{$improved}\n</PROMPT>",
        );
    }

    private function task(string $system, string $user): LlmResponse
    {
        $requirements = $this->extractRequirements($system);
        $structured = $this->structureScore($system) >= 1;

        $parts = [];

        if ($structured) {
            $parts[] = 'Here is the requested response.';
        }

        $parts[] = trim($user) !== '' ? trim($user) : '(no input provided)';

        foreach ($requirements as $requirement) {
            $parts[] = '- '.$requirement;
        }

        return new LlmResponse(implode("\n\n", $parts), Str::wordCount($system), Str::wordCount($user));
    }

    /**
     * Extract explicit requirements from a prompt: bullet and numbered lines.
     *
     * @return list<string>
     */

    /**
     * Diff-explain branch for the [OCTAVIA-DIFF-EXPLAIN] system marker.
     * Deterministic: returns a structured JSON summary of changes.
     */
    private function diffExplain(string $user): LlmResponse
    {
        $from = '';
        $to = '';
        if (preg_match('/---FROM---\s*(.*?)\s*---TO---/s', $user, $m)) {
            $from = trim($m[1]);
            $to = trim((string) preg_replace('/^.*?---TO---\s*/s', '', $user));
        }

        $fromWords = str_word_count($from);
        $toWords = str_word_count($to);
        $addedWords = max(0, $toWords - $fromWords);

        $type = $addedWords > 5 ? 'major' : ($addedWords > 0 ? 'minor' : 'formatting');
        $impact = $addedWords > 5 ? 'positive' : 'neutral';
        $description = match ($type) {
            'major' => 'The new version adds substantial instructions or constraints.',
            'minor' => 'The new version includes small clarifications or formatting.',
            default => 'Only whitespace or formatting changed.',
        };

        $recommendation = str_contains(mb_strtolower($to), 'json')
            ? 'Test the new JSON requirement with nested and empty inputs.'
            : 'Run an evaluation against the current benchmark to confirm the change helps.';

        $json = json_encode([
            'summary' => "Version comparison: from {$fromWords} to {$toWords} words.",
            'changes' => [
                ['type' => $type, 'description' => $description, 'impact' => $impact],
            ],
            'recommendation' => $recommendation,
        ]);

        return new LlmResponse((string) $json, 0, 0);
    }

    private function extractRequirements(string $prompt): array
    {
        $lines = preg_split('/\r?\n/', $prompt) ?: [];
        $requirements = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if (preg_match('/^(?:[-*•]|\d+[.)])\s+(.{4,})$/u', $line, $m)) {
                $requirements[] = trim($m[1], ". \t");
            }
        }

        return array_slice($requirements, 0, 12);
    }

    /**
     * 0-3 structural quality score: role definition, constraints, examples.
     */
    private function structureScore(string $prompt): int
    {
        $score = 0;
        $score += (int) preg_match('/you are|du bist/iu', $prompt);
        $score += (int) preg_match('/constraint|rule|requirement|regel|anforderung/iu', $prompt);
        $score += (int) preg_match('/example|beispiel/iu', $prompt);

        return $score;
    }
}
