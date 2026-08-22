<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PromptResource;
use App\Models\Benchmark;
use App\Models\Prompt;
use App\Services\EvaluationService;
use App\Services\Llm\Contracts\LlmProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class PromptController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return PromptResource::collection(
            Prompt::with('currentVersion')
                ->where('user_id', $request->user()->id)
                ->latest()
                ->paginate(30),
        );
    }

    public function show(Request $request, Prompt $prompt): PromptResource
    {
        abort_unless($prompt->user_id === $request->user()->id, 404);

        return new PromptResource($prompt->load('currentVersion'));
    }

    public function store(Request $request): PromptResource
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'visibility' => ['required', 'in:private,public'],
            'content' => ['required', 'string', 'max:100000'],
        ]);

        $prompt = $request->user()->prompts()->create(collect($validated)->except('content')->all());

        $version = $prompt->versions()->create([
            'version' => 1,
            'content' => $validated['content'],
            'changelog' => 'Created via API',
            'created_at' => now(),
        ]);
        $prompt->update(['current_version_id' => $version->id]);

        return new PromptResource($prompt->load('currentVersion'));
    }

    /**
     * Evaluate the prompt's current content against a benchmark.
     * Runs a single scoring pass synchronously and returns the summary —
     * no Run record is created.
     */
    public function evaluate(Request $request, Prompt $prompt, LlmProvider $provider, EvaluationService $evaluation): Response|JsonResponse
    {
        abort_unless($prompt->user_id === $request->user()->id, 404);

        $validated = $request->validate([
            'benchmark_id' => ['required', 'integer'],
        ]);

        $benchmark = Benchmark::visibleTo($request->user())->find($validated['benchmark_id']);

        if (! $benchmark) {
            return response()->json(['error' => 'Benchmark not found.'], 404);
        }

        $summary = $evaluation->evaluate(
            $provider,
            $prompt->currentContent() ?? '',
            [$benchmark->load('cases.criteria')],
        );

        return response()->json($summary->toArray());
    }

    /**
     * Word-level diff source between two versions of a prompt.
     * Defaults: from = version before `to`, to = current version.
     * The actual diff computation lives client-side (`utils/diff.ts`).
     */
    public function diff(Request $request, Prompt $prompt): JsonResponse
    {
        abort_unless($prompt->user_id === $request->user()->id, 404);

        $validated = $request->validate([
            'from' => ['nullable', 'integer'],
            'to' => ['nullable', 'integer'],
        ]);

        // The versions() relation defaults to newest-first; force chronological.
        $versions = $prompt->versions()->orderBy('version', 'asc')->get(['id', 'version', 'content'])->sortBy('version')->values();

        if ($versions->count() < 2) {
            return response()->json(['error' => 'Prompt needs at least two versions to diff.'], 422);
        }

        $to = ($validated['to'] ?? null)
            ? $versions->firstWhere('version', (int) $validated['to'])
            : $versions->last();

        abort_unless($to, 404);

        $toIndex = (int) $versions->search($to);
        $from = ($validated['from'] ?? null)
            ? $versions->firstWhere('version', (int) $validated['from'])
            : $versions->values()->get(max($toIndex - 1, 0));

        abort_unless($from, 404);

        return response()->json([
            'from' => ['version' => $from->version, 'content' => $from->content],
            'to' => ['version' => $to->version, 'content' => $to->content],
        ]);
    }
}
