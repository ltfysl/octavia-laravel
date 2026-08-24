<?php

namespace App\Http\Controllers;

use App\Services\Llm\Contracts\LlmProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PlaygroundController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('playground/Index');
    }

    public function chat(Request $request, LlmProvider $provider): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:20000'],
            'systemPrompt' => ['nullable', 'string', 'max:20000'],
            'history' => ['nullable', 'array'],
            'history.*.role' => ['required_with:history', Rule::in(['user', 'assistant'])],
            'history.*.content' => ['required_with:history', 'string'],
        ]);

        $messages = [];

        if (! empty($validated['systemPrompt'])) {
            $messages[] = ['role' => 'system', 'content' => $validated['systemPrompt']];
        }

        foreach (array_slice($validated['history'] ?? [], -10) as $msg) {
            $messages[] = ['role' => $msg['role'], 'content' => $msg['content']];
        }

        $messages[] = ['role' => 'user', 'content' => $validated['message']];

        $response = $provider->complete($messages);

        return response()->json([
            'role' => 'assistant',
            'content' => $response->content,
        ]);
    }
}
