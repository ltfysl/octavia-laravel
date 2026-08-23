<?php

namespace App\Services;

use App\Data\Evaluation\EvaluationSummary;
use App\Enums\RunMode;
use App\Enums\RunStatus;
use App\Enums\StepPhase;
use App\Models\Prompt;
use App\Models\PromptVersion;
use App\Models\Run;
use App\Models\RunStep;
use App\Notifications\RunCompletedNotification;
use App\Services\Llm\Contracts\LlmProvider;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Hill-climbing prompt optimizer.
 *
 * Loop: evaluate current prompt -> if target reached or budget exhausted,
 * stop; otherwise ask the provider for an improved prompt given the failing
 * cases, evaluate it next iteration and keep it only when it scores at
 * least as well as the incumbent. Every evaluation is persisted as a
 * RunStep with full per-case detail so the UI can replay the evolution
 * step by step.
 */
class EvolutionService
{
    public const OPTIMIZER_MARKER = '[OCTAVIA-OPTIMIZER]';

    /** Seven strategies — reference parity (test has 7), cycled per generation. */
    private const STRATEGIES = [
        'mutation' => 'Make a small, focused mutation to address the unmet requirements.',
        'crossover' => 'Combine the best prompt with a creative variant to produce a hybrid.',
        'critic' => 'Critique the current prompt and rewrite it to fix the flaws.',
        'compression' => 'Compress the prompt to be more concise while keeping all requirements.',
        'expansion' => 'Expand the prompt with more explicit detail to cover missing criteria.',
        'style_shift' => 'Shift the style and tone while preserving the core intent.',
        'error_driven' => 'Focus solely on the failing cases and fix them directly.',
    ];

    /** Minimum improvement required to reset the stale counter. */
    private const MIN_IMPROVEMENT = 0.01;

    public function __construct(
        private readonly EvaluationService $evaluation,
    ) {}

    public function run(Run $run, LlmProvider $provider): void
    {
        $benchmarks = $run->benchmarks();

        if ($benchmarks === []) {
            $run->forceFill(['status' => RunStatus::Failed, 'error' => 'Run has no benchmarks.', 'finished_at' => now()])->save();

            return;
        }

        $prompt = $run->prompt;
        $currentPrompt = $this->initialContent($run);

        $bestScore = -1.0;
        $bestPrompt = $currentPrompt;
        $staleSteps = 0;
        $number = 0;
        $totalTokens = 0;

        $run->update(['status' => RunStatus::Running, 'started_at' => now()]);

        try {
            while ($number < $run->max_steps) {
                if ($this->cancelled($run)) {
                    return;
                }

                $summary = $this->evaluation->evaluate($provider, $currentPrompt, $benchmarks);
                $totalTokens += $summary->tokensUsed;
                $number++;

                $isBest = $summary->score > $bestScore + self::MIN_IMPROVEMENT || $bestScore < 0;

                DB::transaction(function () use ($run, $number, $currentPrompt, $summary, $isBest) {
                    $step = RunStep::create([
                        'run_id' => $run->id,
                        'number' => $number,
                        'phase' => StepPhase::Evaluate,
                        'prompt_content' => $currentPrompt,
                        'score' => $summary->score,
                        'mutation_type' => $number === 1 ? 'initial' : null,
                        'rationale' => $isBest && $number > 1 ? 'Improved over previous best.' : null,
                        'tokens_used' => $summary->tokensUsed,
                    ]);

                    foreach ($summary->cases as $caseOutcome) {
                        $caseResult = $step->caseResults()->create([
                            'benchmark_case_id' => $caseOutcome->caseId,
                            'score' => $caseOutcome->score,
                            'passed' => $caseOutcome->passed,
                            'output' => $caseOutcome->output,
                            'created_at' => now(),
                        ]);

                        foreach ($caseOutcome->criteria as $criterion) {
                            $caseResult->criteriaResults()->create([
                                'criterion_id' => $criterion->criterionId,
                                'criterion_label' => $criterion->label,
                                'passed' => $criterion->passed,
                                'detail' => $criterion->detail,
                                'created_at' => now(),
                            ]);
                        }
                    }
                });

                if ($isBest) {
                    $bestScore = $summary->score;
                    $bestPrompt = $currentPrompt;
                    $staleSteps = 0;
                } else {
                    $staleSteps++;
                }

                if ($summary->score >= $run->target_score) {
                    break;
                }

                if ($staleSteps >= config('llm.evolution.stale_steps', 3)) {
                    break;
                }

                // Cycle through 7 strategies — reference parity, better than single optimizer prompt
                $strategies = array_keys(self::STRATEGIES);
                $strategy = $strategies[($number - 1) % count($strategies)];
                $proposal = $this->proposeImprovement($provider, $bestPrompt, $summary, $strategy);
                $totalTokens += $proposal['tokens'];

                RunStep::create([
                    'run_id' => $run->id,
                    'number' => ++$number,
                    'phase' => StepPhase::Mutate,
                    'prompt_content' => $proposal['prompt'],
                    'score' => null,
                    'mutation_type' => $strategy,
                    'rationale' => $proposal['rationale'],
                    'tokens_used' => $proposal['tokens'],
                    'created_at' => now(),
                ]);

                $currentPrompt = $proposal['prompt'];
            }

            $this->finish($run, $prompt, $bestPrompt, max($bestScore, 0.0));
        } catch (Throwable $e) {
            report($e);

            $run->forceFill([
                'status' => RunStatus::Failed,
                'error' => mb_substr($e->getMessage(), 0, 2000),
                'finished_at' => now(),
            ])->save();
        }
    }

    /**
     * @return array{prompt: string, rationale: string, tokens: int}
     */
    private function proposeImprovement(LlmProvider $provider, string $prompt, EvaluationSummary $summary, string $strategy): array
    {
        $strategyHint = self::STRATEGIES[$strategy] ?? self::STRATEGIES['mutation'];
        $failures = '';

        foreach ($summary->failingCases() as $case) {
            $failures .= "Case: {$case->title}\n";

            foreach ($case->criteria as $criterion) {
                if (! $criterion->passed) {
                    $failures .= "- Unmet: {$criterion->label}\n";
                }
            }

            $failures .= "\n";
        }

        $response = $provider->complete([
            ['role' => 'system', 'content' => self::OPTIMIZER_MARKER." You are Octavia, an expert prompt engineer. Use strategy '{$strategy}' — {$strategyHint} Improve the given prompt so every unmet requirement is satisfied explicitly. Reply with a short rationale line, then the full improved prompt wrapped in <PROMPT>...</PROMPT> tags."],
            ['role' => 'user', 'content' => "Current prompt:\n<PROMPT>\n{$prompt}\n</PROMPT>\n\nUnmet benchmark requirements:\n{$failures}"],
        ], ['temperature' => 0.4]);

        $improved = $prompt;

        if (preg_match('/<PROMPT>\s*(.*?)\s*<\/PROMPT>/s', $response->content, $m) && trim($m[1]) !== '') {
            $improved = trim($m[1]);
        }

        return [
            'prompt' => $improved,
            'rationale' => trim(preg_replace('/<PROMPT>.*<\/PROMPT>/s', '', $response->content) ?? '') ?: 'Refined prompt based on unmet requirements.',
            'tokens' => $response->totalTokens(),
        ];
    }

    private function finish(Run $run, Prompt $prompt, string $bestPrompt, float $bestScore): void
    {
        // Persist the winning prompt as a new version when it beats the
        // prompt's current content.
        if ($bestPrompt !== '' && trim($bestPrompt) !== trim($prompt->currentContent() ?? '')) {
            $version = PromptVersion::create([
                'prompt_id' => $prompt->id,
                'version' => $prompt->nextVersionNumber(),
                'content' => $bestPrompt,
                'changelog' => "Optimized via run #{$run->id}",
                'created_at' => now(),
            ]);
            $prompt->update(['current_version_id' => $version->id]);
        }

        $run->forceFill([
            'status' => RunStatus::Completed,
            'best_score' => round($bestScore, 3),
            'finished_at' => now(),
        ])->save();

        // Optimize runs take minutes — the user should not have to watch.
        if ($run->mode === RunMode::Optimize) {
            $run->user->notify(new RunCompletedNotification($run->refresh()));
        }
    }

    private function initialContent(Run $run): string
    {
        return $run->prompt->currentContent() ?? '';
    }

    private function cancelled(Run $run): bool
    {
        if ($run->fresh()->status === RunStatus::Cancelled) {
            $run->forceFill(['finished_at' => now()])->save();

            return true;
        }

        return false;
    }
}
