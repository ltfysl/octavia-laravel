<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TournamentController extends Controller
{
    /**
     * Multi-model tournament — stub view. Runs the same prompt against
     * multiple configured providers and ranks them by evaluation score.
     */
    public function index(): Response
    {
        return Inertia::render('tournaments/Index', [
            'providers' => config('llm.providers', []),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'prompt_id' => ['required', 'integer'],
            'benchmark_id' => ['required', 'integer'],
            'providers' => ['required', 'array', 'min:2'],
        ]);

        // Stub: real implementation queues one run per provider.
        return back()->with('success', 'Tournament queued for '.count($data['providers']).' providers (stub)');
    }
}
