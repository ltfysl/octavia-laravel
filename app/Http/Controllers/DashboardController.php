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
