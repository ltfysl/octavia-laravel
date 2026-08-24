<?php

namespace App\Services;

use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\Run;
use App\Models\RunStep;
use App\Models\User;

class DashboardService
{
    /**
     * @return array<string, mixed>
     */
    public function forUser(User $user): array
    {
        $scoreDistribution = $this->scoreDistribution($user);

        return [
            'stats' => $this->stats($user),
            'scoreHistory' => fn () => $this->scoreHistory($user),
            'topPrompts' => fn () => $this->topPrompts($user),
            'recentRuns' => fn () => $this->recentRuns($user),
            'leaderboard' => fn () => $this->leaderboard($user),
            'promptCategories' => fn () => $this->categoryBreakdown(Prompt::class, $user, 'category'),
            'benchmarkCategories' => fn () => $this->categoryBreakdown(Benchmark::class, $user, 'category'),
            'scoreDistribution' => fn () => $scoreDistribution,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function stats(User $user): array
    {
        return [
            'prompts' => Prompt::where('user_id', $user->id)->count(),
            'benchmarks' => Benchmark::where('user_id', $user->id)->count(),
            'activeRuns' => Run::where('user_id', $user->id)->whereIn('status', ['pending', 'running'])->count(),
            'bestScore' => (float) (Run::where('user_id', $user->id)
                ->whereNotNull('best_score')->max('best_score') ?? 0),
        ];
    }

    /**
     * @return array<int, array{at: string, score: float}>
     */
    private function scoreHistory(User $user): array
    {
        return Run::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereNotNull('best_score')
            ->orderByDesc('created_at')
            ->limit(30)
            ->get(['created_at', 'best_score'])
            ->reverse()
            ->map(fn (Run $run) => [
                'at' => $run->created_at->toIso8601String(),
                'score' => $run->best_score,
            ])
            ->all();
    }

    /**
     * @return array<int, array{id: int, name: string, avg_score: float|null, best_score: float|null}>
     */
    private function topPrompts(User $user): array
    {
        return Prompt::where('user_id', $user->id)
            ->whereHas('runs', fn ($q) => $q->where('status', 'completed')->whereNotNull('best_score'))
            ->withAvg('runs as avg_score', 'best_score')
            ->withMax('runs as best_score', 'best_score')
            ->orderByDesc('best_score')
            ->limit(5)
            ->get(['id', 'name'])
            ->map(fn (Prompt $prompt) => [
                'id' => $prompt->id,
                'name' => $prompt->name,
                'avg_score' => $prompt->avg_score ? round($prompt->avg_score * 100, 1) : null,
                'best_score' => $prompt->best_score ? round($prompt->best_score * 100, 1) : null,
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function recentRuns(User $user): array
    {
        return Run::with(['prompt:id,name', 'benchmark:id,name'])
            ->where('user_id', $user->id)
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn (Run $run) => [
                'id' => $run->id,
                'name' => $run->name,
                'status' => $run->status->value,
                'mode' => $run->mode->value,
                'score' => $run->best_score,
                'prompt' => $run->prompt?->only(['id', 'name']),
                'benchmark' => $run->benchmark?->only(['id', 'name']),
                'created_at' => $run->created_at->toIso8601String(),
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function leaderboard(User $user): array
    {
        return RunStep::with('run')
            ->whereHas('run', fn ($q) => $q->visibleTo($user))
            ->whereNotNull('score')
            ->where('score', '>', 0)
            ->orderByDesc('score')
            ->take(5)
            ->get()
            ->map(fn (RunStep $step, int $index) => [
                'rank' => $index + 1,
                'id' => $step->id,
                'run_id' => $step->run_id,
                'run_name' => $step->run->name,
                'prompt_content' => str($step->prompt_content)->limit(80)->value(),
                'score' => round($step->score * 100, 1),
                'strategy' => $step->mutation_type ?? 'Seed',
            ])
            ->all();
    }

    /**
     * @return array<int, array{label: string, count: int, fill: float}>
     */
    private function categoryBreakdown(string $model, User $user, string $column): array
    {
        $rows = $model::query()
            ->where('user_id', $user->id)
            ->selectRaw("{$column}, count(*) as count")
            ->groupBy($column)
            ->orderByDesc('count')
            ->get();

        $total = $rows->sum('count') ?: 1;

        return $rows->map(function ($row) use ($column, $total) {
            $value = $row->{$column};
            $label = is_string($value)
                ? $value
                : (is_object($value) && method_exists($value, 'label') ? $value->label() : (string) $value);

            return [
                'label' => $label,
                'count' => (int) $row->count,
                'fill' => round($row->count / $total * 100, 1),
            ];
        })->all();
    }

    /**
     * @return array<int, array{range: string, count: int}>
     */
    private function scoreDistribution(User $user): array
    {
        $rows = Run::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereNotNull('best_score')
            ->selectRaw(<<<'SQL'
                CASE
                    WHEN best_score >= 0.9 THEN '90–100%'
                    WHEN best_score >= 0.7 THEN '70–89%'
                    WHEN best_score >= 0.5 THEN '50–69%'
                    WHEN best_score >= 0.3 THEN '30–49%'
                    ELSE '0–29%'
                END as `range`,
                count(*) as count
            SQL)
            ->groupBy('range')
            ->orderByDesc('count')
            ->get();

        return $rows->map(fn ($row) => [
            'range' => $row->range,
            'count' => (int) $row->count,
        ])->all();
    }
}
