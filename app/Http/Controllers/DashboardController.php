<?php

namespace App\Http\Controllers;

use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\Run;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('dashboard/Index', [
            'stats' => [
                'prompts' => Prompt::where('user_id', $user->id)->count(),
                'benchmarks' => Benchmark::where('user_id', $user->id)->count(),
                'activeRuns' => Run::where('user_id', $user->id)->whereIn('status', ['pending', 'running'])->count(),
                'bestScore' => (float) (Run::where('user_id', $user->id)
                    ->whereNotNull('best_score')->max('best_score') ?? 0),
            ],
            'scoreHistory' => fn () => Run::where('user_id', $user->id)
                ->where('status', 'completed')
                ->whereNotNull('best_score')
                ->orderByDesc('created_at')
                ->limit(30)
                ->get(['created_at', 'best_score'])
                ->reverse()
                ->map(fn (Run $run) => [
                    'at' => $run->created_at->toIso8601String(),
                    'score' => $run->best_score,
                ]),
            'topPrompts' => fn () => Prompt::where('user_id', $user->id)
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
                ]),
            'recentRuns' => fn () => Run::with(['prompt:id,name', 'benchmark:id,name'])
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
                ]),
        ]);
    }
}
