<?php

namespace App\Http\Controllers;

use App\Models\Prompt;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PromptExportController extends Controller
{
    public function __invoke(Request $request, Prompt $prompt): JsonResponse
    {
        abort_unless($prompt->user_id === $request->user()->id, 403);

        $prompt->load(['versions', 'currentVersion']);

        $payload = [
            'name' => $prompt->name,
            'description' => $prompt->description,
            'visibility' => $prompt->visibility->value,
            'current_version' => $prompt->currentVersion?->version,
            'exported_at' => now()->toIso8601String(),
            'versions' => $prompt->versions->map(fn ($v) => [
                'version' => $v->version,
                'content' => $v->content,
                'changelog' => $v->changelog,
                'created_at' => $v->created_at?->toIso8601String(),
            ]),
        ];

        $filename = 'octavia-prompt-'.\Illuminate\Support\Str::slug($prompt->name).'.json';

        return response()->json($payload)
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
