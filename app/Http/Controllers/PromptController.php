<?php

namespace App\Http\Controllers;

use App\Actions\RunPlayground;
use App\Http\Requests\StorePromptRequest;
use App\Http\Requests\UpdatePromptRequest;
use App\Models\Prompt;
use App\Models\PromptVersion;
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

        $prompt->delete();

        return redirect()->route('prompts.index')->with('success', __('Prompt deleted.'));
    }
}
