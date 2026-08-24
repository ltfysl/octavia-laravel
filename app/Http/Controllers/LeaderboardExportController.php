<?php

namespace App\Http\Controllers;

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

        $steps = RunStep::with('run')
            ->whereHas('run', fn ($q) => $q->visibleTo($user))
            ->whereNotNull('score')
            ->where('score', '>', 0)
            ->orderByDesc('score')
            ->take($limit)
            ->get(['id', 'run_id', 'number', 'prompt_content', 'score', 'mutation_type', 'tokens_used', 'created_at']);

        $filename = 'leaderboard-'.now()->toDateString().'.csv';

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
