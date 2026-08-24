<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientCreditsException;
use App\Jobs\ProcessRunJob;
use App\Models\AuditLog;
use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\Run;
use App\Services\CreditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RunController extends Controller
{
    public function index(Request $request): Response
    {
        $runs = Run::with(['prompt:id,name', 'benchmark:id,name', 'user:id,name'])
            ->visibleTo($request->user())
            ->latest()
            ->paginate(15)
            ->through(fn (Run $run) => [
                'id' => $run->id,
                'name' => $run->name,
                'status' => $run->status->value,
                'mode' => $run->mode->value,
                'score' => $run->best_score,
                'target' => $run->target_score,
                'prompt' => $run->prompt?->only(['id', 'name']),
                'benchmark' => $run->benchmark?->only(['id', 'name']),
                'owner' => $run->user?->only(['id', 'name']),
                'created_at' => $run->created_at->toIso8601String(),
            ]);

        return Inertia::render('runs/Index', ['runs' => $runs]);
    }

    public function create(Request $request): Response
    {
        $providers = collect(config('llm.providers'))->map(fn ($config, $name) => [
            'value' => $name,
            'label' => $name,
            'model' => $config['model'] ?? null,
        ])->values();

        return Inertia::render('runs/Create', [
            'prompts' => Prompt::where('user_id', $request->user()->id)
                ->with('currentVersion:id,prompt_id,version')
                ->get(['id', 'name'])
                ->map(fn (Prompt $p) => ['id' => $p->id, 'name' => $p->name, 'version' => $p->currentVersion?->version]),
            'benchmarks' => Benchmark::where('user_id', $request->user()->id)
                ->withCount('cases')
                ->get(['id', 'name', 'cases_count']),
            'collections' => $request->user()
                ->collections()
                ->withCount('benchmarks')
                ->get(['id', 'name', 'benchmarks_count']),
            'providers' => $providers,
            'costOptimized' => (bool) config('llm.cost_optimized.enabled'),
            'defaultModel' => (string) config('llm.providers.'.config('llm.default').'.model'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'prompt_id' => ['required', 'integer', 'exists:prompts,id'],
            'benchmark_id' => ['required_without:collection_id', 'nullable', 'integer', 'exists:benchmarks,id'],
            'collection_id' => ['required_without:benchmark_id', 'nullable', 'integer', 'exists:benchmark_collections,id'],
            'mode' => ['required', 'in:evaluate,optimize,regression'],
            'max_steps' => ['nullable', 'integer', 'min:1', 'max:20'],
            'target_score' => ['nullable', 'numeric', 'min:0.1', 'max:1'],
            'cost_optimized' => ['nullable', 'boolean'],
            'model' => ['nullable', 'string', 'max:120'],
        ]);

        $prompt = Prompt::where('user_id', $request->user()->id)->findOrFail($validated['prompt_id']);

        $collection = null;
        if (! empty($validated['collection_id'])) {
            $collection = $request->user()->collections()->findOrFail($validated['collection_id']);

            abort_if($collection->benchmarks()->count() === 0, 422, 'Collection has no benchmarks.');

            $runName = "{$prompt->name} × {$collection->name} (".__('benchmarks.collections.count', ['count' => $collection->benchmarks()->count()]).')';
        } else {
            $benchmark = Benchmark::visibleTo($request->user())->findOrFail($validated['benchmark_id']);

            abort_if($benchmark->cases()->count() === 0, 422, 'Benchmark has no test cases.');

            $runName = "{$prompt->name} × {$benchmark->name}";
        }

        // Reserve one credit per planned step up front; unused steps are
        // refunded by RunObserver when the run reaches a terminal state.
        $reserved = $validated['mode'] === 'evaluate' ? 1 : (int) ($validated['max_steps'] ?? config('llm.evolution.max_steps'));

        try {
            app(CreditService::class)->consume(
                $request->user(),
                $reserved,
                CreditService::REASON_RUN_RESERVED,
                ['run_name' => $runName],
            );
        } catch (InsufficientCreditsException $e) {
            return back()->withErrors([
                'credits' => __('billing.insufficient', [
                    'balance' => $e->balance,
                    'needed' => $e->requested,
                ]),
            ]);
        }

        $run = $request->user()->runs()->create([
            'prompt_id' => $prompt->id,
            'benchmark_id' => $benchmark->id ?? null,
            'collection_id' => $collection?->id,
            'name' => $runName,
            'mode' => $validated['mode'],
            'status' => 'pending',
            'provider' => config('llm.default'),
            'model' => $validated['model'] ?? null,
            'cost_optimized' => $validated['cost_optimized'] ?? false,
            'max_steps' => $validated['max_steps'] ?? config('llm.evolution.max_steps'),
            'target_score' => $validated['target_score'] ?? config('llm.evolution.target_score'),
        ]);

        AuditLog::record('run.started', 'runs', 'Run started ('.($validated['mode'] ?? 'evaluate').')', 'run', (string) $run->id, $run->name);

        ProcessRunJob::dispatch($run->id);

        return redirect()->route('runs.show', $run);
    }

    public function show(Request $request, Run $run): Response
    {
        $this->authorize('view', $run);

        $run->load([
            'prompt:id,name',
            'benchmark:id,name',
            'steps.caseResults.benchmarkCase:id,title',
            'steps.caseResults.criteriaResults',
        ]);

        return Inertia::render('runs/Show', [
            'run' => [
                'id' => $run->id,
                'name' => $run->name,
                'status' => $run->status->value,
                'mode' => $run->mode->value,
                'best_score' => $run->best_score,
                'target_score' => $run->target_score,
                'error' => $run->error,
                'regression_report' => $run->regression_report,
                'prompt' => $run->prompt?->only(['id', 'name']),
                'benchmark' => $run->benchmark?->only(['id', 'name']),
                'created_at' => $run->created_at?->toIso8601String(),
                'finished_at' => $run->finished_at?->toIso8601String(),
                'steps' => $run->steps->map(fn ($step) => [
                    'id' => $step->id,
                    'number' => $step->number,
                    'phase' => $step->phase->value,
                    'score' => $step->score,
                    'mutation_type' => $step->mutation_type,
                    'rationale' => $step->rationale,
                    'tokens_used' => $step->tokens_used,
                    'prompt_content' => $step->prompt_content,
                    'cases' => $step->caseResults->map(fn ($result) => [
                        'id' => $result->id,
                        'title' => $result->benchmarkCase?->title ?? __('Deleted case'),
                        'score' => (float) $result->score,
                        'passed' => $result->passed,
                        'output' => $result->output,
                        'criteria' => $result->criteriaResults->map(fn ($c) => [
                            'label' => $c->criterion_label,
                            'passed' => $c->passed,
                            'detail' => $c->detail,
                        ]),
                    ]),
                ]),
            ],
        ]);
    }

    /** Lightweight polling endpoint for in-progress runs. */
    public function status(Request $request, Run $run): JsonResponse
    {
        $this->authorize('view', $run);

        return response()->json([
            'status' => $run->status->value,
            'best_score' => $run->best_score,
            'steps' => $run->steps()->count(),
        ]);
    }

    public function cancel(Request $request, Run $run): RedirectResponse
    {
        $this->authorize('update', $run);

        if (! $run->isFinished()) {
            $run->forceFill(['status' => 'cancelled', 'finished_at' => now()])->save();

            AuditLog::record('run.cancelled', 'runs', 'Run cancelled', 'run', (string) $run->id, $run->name, 'warning');
        }

        return back()->with('success', __('Run cancelled.'));
    }
}
