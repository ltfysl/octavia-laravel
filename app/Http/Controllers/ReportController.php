<?php

namespace App\Http\Controllers;

use App\Enums\RunStatus;
use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\Run;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        $runs = Run::query()->where('user_id', $user->id);

        $stats = [
            'total' => (clone $runs)->count(),
            'completed' => (clone $runs)->where('status', RunStatus::Completed)->count(),
            'avg_score' => (clone $runs)->whereNotNull('best_score')->avg('best_score') ?? 0,
        ];

        $byBenchmark = Benchmark::query()
            ->where('user_id', $user->id)
            ->withCount(['runs'])
            ->withAvg('runs as avg_score', 'best_score')
            ->orderByDesc('runs_count')
            ->limit(10)
            ->get()
            ->map(fn (Benchmark $b) => [
                'id' => $b->id,
                'name' => $b->name,
                'runs_count' => $b->runs_count,
                'avg_score' => $b->avg_score ? round($b->avg_score * 100, 1) : null,
                'last_run' => $b->runs()->latest('created_at')->value('created_at'),
            ]);

        $byPrompt = Prompt::query()
            ->where('user_id', $user->id)
            ->withCount(['runs'])
            ->withAvg('runs as avg_score', 'best_score')
            ->orderByDesc('runs_count')
            ->limit(10)
            ->get()
            ->map(fn (Prompt $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'runs_count' => $p->runs_count,
                'avg_score' => $p->avg_score ? round($p->avg_score * 100, 1) : null,
                'last_run' => $p->runs()->latest('created_at')->value('created_at'),
            ]);

        $recentRuns = (clone $runs)
            ->with(['prompt:id,name', 'benchmark:id,name'])
            ->latest('created_at')
            ->limit(10)
            ->get()
            ->map(fn (Run $run) => [
                'id' => $run->id,
                'name' => $run->name,
                'status' => $run->status->value,
                'mode' => $run->mode->value,
                'best_score' => $run->best_score,
                'prompt' => $run->prompt?->name,
                'benchmark' => $run->benchmark?->name,
                'created_at' => $run->created_at,
            ]);

        return Inertia::render('reports/Index', [
            'stats' => $stats,
            'byBenchmark' => $byBenchmark,
            'byPrompt' => $byPrompt,
            'recentRuns' => $recentRuns,
        ]);
    }
}
