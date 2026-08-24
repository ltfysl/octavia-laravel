<?php

namespace App\Http\Controllers;

use App\Models\Benchmark;
use App\Models\Run;
use App\Models\RunStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response as ResponseFacade;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeaderboardExportController extends Controller
{
    public function __invoke(Request $request): StreamedResponse
    {
        $user = $request->user();
        $limit = min(max((int) $request->input('limit', 50), 1), 1000);
        $runId = $request->input('run_id') !== null ? (int) $request->input('run_id') : null;
        $benchmarkId = $request->input('benchmark_id') !== null ? (int) $request->input('benchmark_id') : null;

        if ($runId !== null) {
            $this->authorize('view', Run::findOrFail($runId));
        }

        if ($benchmarkId !== null) {
            $this->authorize('view', Benchmark::findOrFail($benchmarkId));
        }

        $steps = RunStep::with('run')
            ->whereHas('run', function ($query) use ($user, $runId, $benchmarkId) {
                $query->visibleTo($user);

                if ($runId !== null) {
                    $query->where('id', $runId);
                }

                if ($benchmarkId !== null) {
                    $query->where('benchmark_id', $benchmarkId);
                }
            })
            ->whereNotNull('score')
            ->where('score', '>', 0)
            ->orderByDesc('score')
            ->take($limit)
            ->get(['id', 'run_id', 'number', 'prompt_content', 'score', 'mutation_type', 'tokens_used', 'created_at']);

        $filenameParts = ['leaderboard'];

        if ($benchmarkId !== null) {
            $filenameParts[] = 'benchmark-'.$benchmarkId;
        }

        if ($runId !== null) {
            $filenameParts[] = 'run-'.$runId;
        }

        $filename = implode('-', $filenameParts).'-'.now()->toDateString().'.csv';

        return ResponseFacade::stream(function () use ($steps) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Rank', 'Candidate', 'Score', 'Step', 'Strategy', 'Tokens', 'Created At']);

            foreach ($steps as $index => $step) {
                fputcsv($handle, [
                    $index + 1,
                    str($step->prompt_content)->limit(100),
                    round($step->score * 100, 2).' %',
                    $step->number,
                    $step->mutation_type ?? 'Seed',
                    $step->tokens_used,
                    $step->created_at->toIso8601String(),
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'no-store',
        ]);
    }
}
