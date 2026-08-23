<?php

namespace App\Http\Controllers;

use App\Actions\RunPlayground;
use App\Enums\RunStatus;
use App\Http\Requests\StorePromptRequest;
use App\Http\Requests\UpdatePromptRequest;
use App\Models\AuditLog;
use App\Models\Prompt;
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

    public function create(): Response
    {
        return Inertia::render('prompts/Create');
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
        ]);
    }
}
