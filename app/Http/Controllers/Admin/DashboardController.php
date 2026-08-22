<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Benchmark;
use App\Models\MarketplaceItem;
use App\Models\Prompt;
use App\Models\Run;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        return Inertia::render('admin/Index', [
            'stats' => [
                'users' => User::count(),
                'prompts' => Prompt::count(),
                'benchmarks' => Benchmark::count(),
                'runs' => Run::count(),
                'activeRuns' => Run::whereIn('status', ['pending', 'running'])->count(),
                'failedRuns' => Run::where('status', 'failed')->count(),
                'marketplaceItems' => MarketplaceItem::listed()->count(),
            ],
            'recentUsers' => fn () => User::query()
                ->latest()
                ->limit(8)
                ->get(['id', 'name', 'email', 'is_admin', 'created_at'])
                ->map(fn (User $user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_admin' => $user->is_admin,
                    'created_at' => $user->created_at?->toIso8601String(),
                ]),
            'recentRuns' => fn () => Run::with(['prompt:id,name', 'benchmark:id,name', 'user:id,name'])
                ->latest()
                ->limit(8)
                ->get()
                ->map(fn (Run $run) => [
                    'id' => $run->id,
                    'name' => $run->name,
                    'status' => $run->status->value,
                    'score' => $run->best_score,
                    'user' => $run->user?->name,
                    'created_at' => $run->created_at?->toIso8601String(),
                ]),
        ]);
    }
}
