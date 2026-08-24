<?php

namespace App\Http\Controllers;

use App\Actions\RunPlayground;
use App\Actions\RunPlaygroundChat;
use App\Enums\RunStatus;
use App\Http\Requests\StorePromptRequest;
use App\Http\Requests\UpdatePromptRequest;
use App\Models\AuditLog;
use App\Models\Prompt;
use App\Models\PromptTemplate;
use App\Models\PromptVersion;
use App\Services\DiffService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PromptController extends Controller
{
    public function index(Request $request): Response
    {
        $prompts = Prompt::with(['currentVersion:id,prompt_id,version'])
            ->withCount('runs')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(12)
            ->through(fn (Prompt $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'description' => $p->description,
                'visibility' => $p->visibility->value,
                'version' => $p->currentVersion?->version,
                'runs_count' => $p->runs_count,
                'updated_at' => $p->updated_at->toIso8601String(),
            ]);

        return Inertia::render('prompts/Index', ['prompts' => $prompts]);
    }

    public function create(Request $request): Response
    {
        $template = null;
        if ($request->filled('template')) {
            $template = PromptTemplate::find($request->input('template'));
        }

        return Inertia::render('prompts/Create', [
            'template' => $template ? [
                'id' => $template->id,
                'name' => $template->name,
                'description' => $template->description,
                'body' => $template->body,
                'category' => $template->category,
            ] : null,
        ]);
    }

    public function templates(): Response
    {
        return Inertia::render('prompts/Templates');
    }

    public function store(StorePromptRequest $request): RedirectResponse
    {
        $prompt = $request->user()->prompts()->create($request->safe()->except('content'));

        $version = $prompt->versions()->create([
            'version' => 1,
            'content' => $request->validated('content'),
            'changelog' => 'Initial version',
            'created_at' => now(),
        ]);

        $prompt->update(['current_version_id' => $version->id]);

        AuditLog::record('prompt.created', 'prompts', 'Prompt created', 'prompt', (string) $prompt->id, $prompt->name);

        return redirect()->route('prompts.show', $prompt)->with('success', __('Prompt created.'));
    }

    public function show(Request $request, Prompt $prompt): Response
    {
        $this->authorize('view', $prompt);

        $prompt->load(['versions', 'currentVersion']);

        return Inertia::render('prompts/Show', [
            'prompt' => [
                'id' => $prompt->id,
                'name' => $prompt->name,
                'description' => $prompt->description,
                'visibility' => $prompt->visibility->value,
                'content' => $prompt->currentVersion?->content,
                'current_version' => $prompt->currentVersion?->version,
                'versions' => $prompt->versions->map(fn (PromptVersion $v) => [
                    'id' => $v->id,
                    'version' => $v->version,
                    'content' => $v->content,
                    'changelog' => $v->changelog,
                    'created_at' => $v->created_at?->toIso8601String(),
                ]),
            ],
            'benchmarks' => $request->user()->benchmarks()->withCount('cases')->get(['id', 'name', 'cases_count']),
        ]);
    }

    /**
     * Ad-hoc playground: run the prompt against one input, no persistence.
     */
    public function playground(Request $request, Prompt $prompt, RunPlayground $runPlayground): JsonResponse
    {
        $this->authorize('view', $prompt);

        $validated = $request->validate([
            'input' => ['required', 'string', 'max:20000'],
            'content' => ['nullable', 'string', 'max:100000'],
        ]);

        return response()->json($runPlayground(
            $prompt,
            $validated['input'],
            $validated['content'] ?? null,
        ));
    }

    /**
     * Multi-turn chat playground for a prompt, no persistence.
     */
    public function playgroundChat(Request $request, Prompt $prompt, RunPlaygroundChat $chat): JsonResponse
    {
        $this->authorize('view', $prompt);

        $validated = $request->validate([
            'messages' => ['required', 'array', 'min:1'],
            'messages.*.role' => ['required', 'in:user,assistant'],
            'messages.*.content' => ['required', 'string', 'max:20000'],
            'content' => ['nullable', 'string', 'max:100000'],
        ]);

        return response()->json($chat(
            $prompt,
            $validated['messages'],
            $validated['content'] ?? null,
        ));
    }

    /**
     * Line diff between two versions of the prompt (JSON for the versions tab).
     */
    public function diff(Request $request, Prompt $prompt, DiffService $diff): JsonResponse
    {
        $this->authorize('view', $prompt);

        $validated = $request->validate([
            'from' => ['required', 'integer'],
            'to' => ['required', 'integer', 'different:from'],
        ]);

        $versions = $prompt->versions()
            ->whereIn('id', [$validated['from'], $validated['to']])
            ->get()
            ->keyBy('id');

        abort_unless(
            $versions->count() === 2,
            404,
            __('Both versions must belong to this prompt.'),
        );

        $from = $versions[$validated['from']];
        $to = $versions[$validated['to']];

        return response()->json([
            'from' => ['id' => $from->id, 'version' => $from->version],
            'to' => ['id' => $to->id, 'version' => $to->version],
            'ops' => $diff->lineDiff((string) $from->content, (string) $to->content),
        ]);
    }

    public function update(UpdatePromptRequest $request, Prompt $prompt): RedirectResponse
    {
        $this->authorize('update', $prompt);

        $data = $request->safe()->except(['content', 'changelog']);
        $prompt->update($data);

        if ($request->filled('content') && $request->input('content') !== $prompt->currentVersion?->content) {
            $version = $prompt->versions()->create([
                'version' => $prompt->nextVersionNumber(),
                'content' => $request->input('content'),
                'changelog' => $request->input('changelog') ?? 'Manual edit',
                'created_at' => now(),
            ]);
            $prompt->update(['current_version_id' => $version->id]);
        }

        AuditLog::record('prompt.updated', 'prompts', 'Prompt updated', 'prompt', (string) $prompt->id, $prompt->name);

        return back()->with('success', __('Prompt saved.'));
    }

    public function restoreVersion(Request $request, Prompt $prompt, PromptVersion $version): RedirectResponse
    {
        $this->authorize('update', $prompt);

        abort_unless($version->prompt_id === $prompt->id, 404);

        $new = $prompt->versions()->create([
            'version' => $prompt->nextVersionNumber(),
            'content' => $version->content,
            'changelog' => "Restored from v{$version->version}",
            'created_at' => now(),
        ]);
        $prompt->update(['current_version_id' => $new->id]);

        return back()->with('success', __('Version restored.'));
    }

    public function destroy(Request $request, Prompt $prompt): RedirectResponse
    {
        $this->authorize('delete', $prompt);

        $name = $prompt->name;
        $id = (string) $prompt->id;

        $prompt->delete();

        AuditLog::record('prompt.deleted', 'prompts', 'Prompt deleted', 'prompt', $id, $name, 'warning');

        return redirect()->route('prompts.index')->with('success', __('messages.promptDeleted'));
    }

    /**
     * Per-prompt analytics: score-over-time and per-benchmark breakdown.
     */
    public function analytics(Request $request, Prompt $prompt): JsonResponse
    {
        $this->authorize('view', $prompt);

        $runs = $prompt->runs()->where('status', RunStatus::Completed->value)->whereNotNull('best_score');
        $avgScore = $runs->avg('best_score');
        $bestScore = $runs->max('best_score');

        $history = $runs->orderBy('created_at')
            ->get(['created_at', 'best_score'])
            ->map(fn ($r) => [
                'at' => $r->created_at->toIso8601String(),
                'score' => $r->best_score,
            ]);

        $byBenchmark = $prompt->runs()
            ->where('status', RunStatus::Completed->value)
            ->whereNotNull('best_score')
            ->join('benchmarks', 'benchmarks.id', '=', 'runs.benchmark_id')
            ->selectRaw('benchmarks.name, count(*) as runs_count, avg(best_score) as avg_score, max(best_score) as best_score')
            ->groupBy('benchmarks.id', 'benchmarks.name')
            ->orderByDesc('avg_score')
            ->get();

        $recentRuns = $prompt->runs()
            ->with('benchmark:id,name')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get(['id', 'benchmark_id', 'name', 'status', 'best_score', 'mode', 'created_at'])
            ->map(fn ($run) => [
                'id' => $run->id,
                'name' => $run->name,
                'status' => $run->status->value,
                'mode' => $run->mode->value,
                'best_score' => $run->best_score ? round($run->best_score * 100, 1) : null,
                'benchmark' => $run->benchmark?->only(['id', 'name']),
                'created_at' => $run->created_at->toIso8601String(),
            ]);

        $scoreDistribution = $prompt->runs()
            ->where('status', RunStatus::Completed->value)
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
            ->get()
            ->map(fn ($row) => [
                'range' => $row->range,
                'count' => (int) $row->count,
            ]);

        return response()->json([
            'runs_count' => $prompt->runs()->count(),
            'completed_count' => $runs->count(),
            'avg_score' => $avgScore !== null ? round((float) $avgScore * 100, 1) : null,
            'best_score' => $bestScore !== null ? round((float) $bestScore * 100, 1) : null,
            'history' => $history,
            'by_benchmark' => $byBenchmark->map(fn ($b) => [
                'name' => $b->name,
                'runs_count' => $b->runs_count,
                'avg_score' => $b->avg_score !== null ? round((float) $b->avg_score * 100, 1) : null,
                'best_score' => $b->best_score !== null ? round((float) $b->best_score * 100, 1) : null,
            ]),
            'recent_runs' => $recentRuns,
            'score_distribution' => $scoreDistribution,
        ]);
    }
}
