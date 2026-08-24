<?php

namespace App\Services;

use App\Data\Evaluation\CaseOutcome;
use App\Data\Evaluation\CriterionOutcome;
use App\Data\Evaluation\EvaluationSummary;
use App\Enums\CriterionType;
use App\Models\Benchmark;
use App\Models\BenchmarkCase;
use App\Models\BenchmarkCriterion;
use App\Services\Llm\Contracts\LlmProvider;
use Illuminate\Support\Str;

/**
 * Runs a prompt against benchmarks and produces deterministic, auditable
 * scores. Programmatic criteria (contains / not_contains / regex) are
 * evaluated locally; llm_judge criteria are scored by the configured
 * provider acting as a strict judge.
 */
class EvaluationService
{
    /** LLM judge scores at or above this threshold count as passed. */
    public const JUDGE_PASS_THRESHOLD = 0.7;

    /** A benchmark case passes when its score reaches this value. */
    public const CASE_PASS_THRESHOLD = 0.8;

    /** Marker that lets deterministic providers detect judge requests. */
    public const JUDGE_MARKER = '[OCTAVIA-JUDGE]';

    public function evaluate(LlmProvider $provider, string $promptContent, iterable $benchmarks, array $options = []): EvaluationSummary
    {
        $cases = [];
        $tokens = 0;
        $totalWeight = 0.0;
        $weightedScore = 0.0;

        foreach ($benchmarks as $benchmark) {
            foreach ($benchmark->cases as $case) {
                $weight = max($case->weight, 0.01);
                $totalWeight += $weight;

                $taskOptions = ['temperature' => 0.7];

                if (isset($options['model'])) {
                    $taskOptions['model'] = $options['model'];
                }

                $output = $provider->complete(
                    $this->taskMessages($promptContent, $case->input),
                    $taskOptions,
                );
                $tokens += $output->totalTokens();

                $outcome = $this->scoreCase($provider, $case, $output->content, $options['model'] ?? null);
                $cases[] = $outcome;
                $weightedScore += $outcome->score * $weight;
            }
        }

        $score = $totalWeight > 0 ? $weightedScore / $totalWeight : 0.0;

        return new EvaluationSummary(round($score, 3), $cases, $tokens);
    }

    private function scoreCase(LlmProvider $provider, BenchmarkCase $case, string $output, ?string $model = null): CaseOutcome
    {
        $criteria = [];
        $criterionScores = [];

        foreach ($case->criteria as $criterion) {
            $outcome = $this->scoreCriterion($provider, $criterion, $output, $model);
            $criteria[] = $outcome;
            $criterionScores[] = $outcome->score;
        }

        $score = $criterionScores === []
            ? 0.0
            : array_sum($criterionScores) / count($criterionScores);

        return new CaseOutcome(
            caseId: $case->id,
            title: $case->title,
            weight: (float) $case->weight,
            score: $score,
            passed: $score >= self::CASE_PASS_THRESHOLD,
            output: $output,
            criteria: $criteria,
        );
    }

    private function scoreCriterion(LlmProvider $provider, BenchmarkCriterion $criterion, string $output, ?string $model = null): CriterionOutcome
    {
        return match ($criterion->type) {
            CriterionType::Contains => $this->scoreContains($criterion, $output, expected: true),
            CriterionType::NotContains => $this->scoreContains($criterion, $output, expected: false),
            CriterionType::Regex => $this->scoreRegex($criterion, $output),
            CriterionType::LlmJudge => $this->scoreLlmJudge($provider, $criterion, $output, $model),
        };
    }

    private function scoreContains(BenchmarkCriterion $criterion, string $output, bool $expected): CriterionOutcome
    {
        $needles = (array) ($criterion->config['values'] ?? []);
        $missing = [];

        foreach ($needles as $needle) {
            if (! Str::contains(mb_strtolower($output), mb_strtolower(trim((string) $needle)))) {
                $missing[] = $needle;
            }
        }

        $passed = $expected ? $missing === [] : $missing !== [];
        $score = $needles === [] ? 1.0 : ($passed ? 1.0 : ($expected ? (count($needles) - count($missing)) / count($needles) : 0.0));

        return new CriterionOutcome(
            criterionId: $criterion->id,
            label: $criterion->label,
            type: $criterion->type->value,
            passed: $passed,
            score: $score,
            detail: $expected ? ['missing' => $missing] : ['forbidden_found' => array_diff($needles, $missing)],
        );
    }

    private function scoreRegex(BenchmarkCriterion $criterion, string $output): CriterionOutcome
    {
        $pattern = (string) ($criterion->config['pattern'] ?? '');

        if (trim($pattern) === '' || @preg_match($pattern, '') === false) {
            return new CriterionOutcome($criterion->id, $criterion->label, $criterion->type->value, false, 0.0, ['error' => 'invalid_pattern']);
        }

        $matched = preg_match($pattern, $output) === 1;

        return new CriterionOutcome($criterion->id, $criterion->label, $criterion->type->value, $matched, $matched ? 1.0 : 0.0);
    }

    private function scoreLlmJudge(LlmProvider $provider, BenchmarkCriterion $criterion, string $output, ?string $model = null): CriterionOutcome
    {
        $description = (string) ($criterion->config['description'] ?? $criterion->label);

        $judgeOptions = ['temperature' => 0.0];

        if ($model !== null) {
            $judgeOptions['model'] = $model;
        }

        $response = $provider->complete($this->judgeMessages($description, $output), $judgeOptions);
        $score = $this->parseJudgeScore($response->content);

        return new CriterionOutcome(
            criterionId: $criterion->id,
            label: $criterion->label,
            type: $criterion->type->value,
            passed: $score >= self::JUDGE_PASS_THRESHOLD,
            score: $score,
            detail: ['judge_raw' => Str::limit($response->content, 300)],
        );
    }

    private function parseJudgeScore(string $content): float
    {
        if (preg_match('/SCORE\s*[:=]\s*([0-9]+(?:\.[0-9]+)?)/i', $content, $m)) {
            return min(1.0, max(0.0, (float) $m[1]));
        }

        return 0.0;
    }

    /**
     * @return list<array{role: string, content: string}>
     */
    private function taskMessages(string $prompt, string $input): array
    {
        return [
            ['role' => 'system', 'content' => $prompt],
            ['role' => 'user', 'content' => $input],
        ];
    }

    /**
     * @return list<array{role: string, content: string}>
     */
    private function judgeMessages(string $description, string $output): array
    {
        return [
            ['role' => 'system', 'content' => self::JUDGE_MARKER.' You are a strict, impartial evaluation judge. Respond with exactly one line: "SCORE: <0.00-1.00>".'],
            ['role' => 'user', 'content' => "Requirement: {$description}\n\nModel output to judge:\n---\n{$output}\n---\n\nEnd your reply with the final line \"SCORE: <float>\"."],
        ];
    }
}
