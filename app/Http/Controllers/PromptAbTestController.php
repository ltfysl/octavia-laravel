<?php

namespace App\Http\Controllers;

use App\Actions\RunPromptAbTest;
use App\Http\Requests\StorePromptAbTestRequest;
use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\PromptVersion;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PromptAbTestController extends Controller
{
    public function __invoke(StorePromptAbTestRequest $request, Prompt $prompt, RunPromptAbTest $abTest): JsonResponse
    {
        $this->authorize('view', $prompt);

        $validated = $request->validated();

        $versionA = PromptVersion::where('prompt_id', $prompt->id)->findOrFail($validated['version_a_id']);
        $versionB = PromptVersion::where('prompt_id', $prompt->id)->findOrFail($validated['version_b_id']);

        $benchmark = Benchmark::where('user_id', Auth::id())->findOrFail($validated['benchmark_id']);

        $result = $abTest($versionA, $versionB, $benchmark);

        return response()->json($result);
    }
}
