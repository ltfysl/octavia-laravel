<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RunResource;
use App\Jobs\ProcessRunJob;
use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\Run;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class RunController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return RunResource::collection(
            Run::with(['prompt:id,name', 'benchmark:id,name'])
                ->where('user_id', $request->user()->id)
                ->latest()
                ->paginate(30),
        );
    }

    public function show(Request $request, Run $run): RunResource
    {
        abort_unless($run->user_id === $request->user()->id, 404);

        return new RunResource($run->load([
            'prompt:id,name',
            'benchmark:id,name',
            'steps',
        ]));
    }

    public function store(Request $request): JsonResource|JsonResponse
    {
        $validated = $request->validate([
            'prompt_id' => ['required', 'integer'],
            'benchmark_id' => ['required_without:collection_id', 'nullable', 'integer'],
            'collection_id' => ['required_without:benchmark_id', 'nullable', 'integer'],
            'mode' => ['required', 'in:evaluate,optimize'],
            'max_steps' => ['nullable', 'integer', 'min:1', 'max:20'],
            'target_score' => ['nullable', 'numeric', 'min:0.1', 'max:1'],
        ]);

        $prompt = Prompt::where('user_id', $request->user()->id)->findOrFail($validated['prompt_id']);

        $benchmark = null;
        $collectionId = null;

        if (! empty($validated['collection_id'])) {
            $collection = $request->user()->collections()->findOrFail($validated['collection_id']);
            $collectionId = $collection->id;
            $name = "{$prompt->name} × {$collection->name}";
        } else {
            $benchmark = Benchmark::visibleTo($request->user())->findOrFail($validated['benchmark_id']);
            $name = "{$prompt->name} × {$benchmark->name}";
        }

        $run = $request->user()->runs()->create([
            'prompt_id' => $prompt->id,
            'benchmark_id' => $benchmark?->id,
            'collection_id' => $collectionId,
            'name' => $name,
            'mode' => $validated['mode'],
            'status' => 'pending',
            'provider' => config('llm.default'),
            'max_steps' => $validated['max_steps'] ?? config('llm.evolution.max_steps'),
            'target_score' => $validated['target_score'] ?? config('llm.evolution.target_score'),
        ]);

        ProcessRunJob::dispatch($run->id);

        return (new RunResource($run->load('prompt:id,name')))
            ->response()
            ->setStatusCode(201);
    }

    public function cancel(Request $request, Run $run): RunResource
    {
        abort_unless($run->user_id === $request->user()->id, 404);

        if (! $run->isFinished()) {
            $run->forceFill(['status' => 'cancelled', 'finished_at' => now()])->save();
        }

        return new RunResource($run);
    }
}
