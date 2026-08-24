<?php

namespace App\Http\Controllers;

use App\Actions\RunPromptRegressionTest;
use App\Http\Requests\StorePromptRegressionTestRequest;
use App\Models\Benchmark;
use App\Models\Prompt;
use Illuminate\Http\JsonResponse;

class PromptRegressionTestController extends Controller
{
    public function __invoke(StorePromptRegressionTestRequest $request, Prompt $prompt, RunPromptRegressionTest $regression): JsonResponse
    {
        $this->authorize('view', $prompt);

        $validated = $request->validated();
        $benchmarkIds = $validated['benchmark_ids'] ?? null;

        if ($benchmarkIds !== null) {
            $visibleIds = Benchmark::visibleTo($request->user())
                ->whereIn('id', $benchmarkIds)
                ->pluck('id')
                ->all();

            abort_if(count($visibleIds) !== count($benchmarkIds), 422);

            $benchmarkIds = $visibleIds;
        }

        $result = $regression($prompt, $benchmarkIds, $validated['sample_input'] ?? null);

        return response()->json($result);
    }
}
