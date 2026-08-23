<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessRunJob;
use App\Models\AuditLog;
use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\Run;
use App\Services\Llm\LlmManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TournamentController extends Controller
{
    /**
     * Multi-model tournament: run the same prompt against the same
     * benchmark on every selected provider and rank the results.
     */
    public function index(Request $request, LlmManager $manager): Response
    {
        $providers = collect(config('llm.providers', []))
            ->map(fn (array $config, string $name) => [
                'name' => $name,
                'driver' => $config['driver'],
                'configured' => $manager->isConfigured($name),
            ])
            ->values();

        $runIds = collect(explode(',', (string) $request->query('runs', '')))
            ->filter(fn ($v) => is_numeric($v))
            ->map(fn ($v) => (int) $v)
            ->values();

        $runs = collect();
        if ($runIds->isNotEmpty()) {
            $runs = Run::whereIn('id', $runIds)
                ->where('user_id', $request->user()->id)
                ->with('benchmark:id,name')
                ->get()
                ->sortByDesc(fn (Run $run) => (float) ($run->best_score ?? -1))
                ->values();
        }

        return Inertia::render('tournaments/Index', [
            'prompts' => $request->user()->prompts()->get(['id', 'name']),
            'benchmarks' => Benchmark::visibleTo($request->user())->get(['id', 'name']),
            'results' => $runs->map(fn (Run $run) => [
                'id' => $run->id,
                'provider' => $run->provider,
                'status' => $run->status->value,
                'best_score' => $run->best_score,
                'benchmark_name' => $run->benchmark?->name,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'prompt_id' => ['required', 'integer'],
            'benchmark_id' => ['required', 'integer'],
            'providers' => ['required', 'array', 'min:2'],
            'providers.*' => ['string'],
        ]);

        $prompt = Prompt::where('user_id', $request->user()->id)->findOrFail($validated['prompt_id']);
        /** @var Benchmark $benchmark */
        $benchmark = Benchmark::visibleTo($request->user())->findOrFail($validated['benchmark_id']);

        $manager = app(LlmManager::class);
        $configured = collect($validated['providers'])
            ->unique()
            ->filter(fn (string $name) => array_key_exists($name, config('llm.providers', [])))
            ->filter(fn (string $name) => $manager->isConfigured($name))
            ->values();

        if ($configured->count() < 2) {
            return back()->withErrors([
                'providers' => __('At least two configured providers are required for a tournament.'),
            ]);
        }

        $runIds = $configured->map(function (string $provider) use ($request, $prompt, $benchmark) {
            $run = $request->user()->runs()->create([
                'prompt_id' => $prompt->id,
                'collection_id' => null,
                'benchmark_id' => $benchmark->id,
                'name' => "{$prompt->name} × {$benchmark->name} · {$provider}",
                'mode' => 'evaluate',
                'status' => 'pending',
                'provider' => $provider,
                'max_steps' => 1,
                'target_score' => config('llm.evolution.target_score'),
            ]);

            ProcessRunJob::dispatch($run->id);

            return $run->id;
        });

        AuditLog::record(
            'tournament.started',
            'tournaments',
            "Tournament started with {$configured->count()} providers",
            'prompt',
            (string) $prompt->id,
            $prompt->name,
            metadata: ['providers' => $configured->all(), 'runs' => $runIds->all()],
        );

        return redirect()->route('tournaments.index', ['runs' => $runIds->implode(',')])
            ->with('success', __('Tournament started.'));
    }
}
