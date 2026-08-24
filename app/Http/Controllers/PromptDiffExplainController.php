<?php

namespace App\Http\Controllers;

use App\Models\Prompt;
use App\Services\DiffExplainService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PromptDiffExplainController extends Controller
{
    public function __construct(private readonly DiffExplainService $service) {}

    public function __invoke(Request $request, Prompt $prompt): JsonResponse
    {
        $this->authorize('view', $prompt);

        $validated = $request->validate([
            'from_version_id' => ['required', 'integer', 'exists:prompt_versions,id'],
            'to_version_id' => ['required', 'integer', 'exists:prompt_versions,id'],
        ]);

        $from = $prompt->versions()->findOrFail($validated['from_version_id']);
        $to = $prompt->versions()->findOrFail($validated['to_version_id']);

        return response()->json($this->service->explain($from, $to));
    }
}
