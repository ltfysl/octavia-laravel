<?php

namespace App\Http\Controllers;

use App\Models\ApiTokenUse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Sanctum\PersonalAccessToken;

class ApiKeyController extends Controller
{
    public function index(Request $request): Response
    {
        $tokens = $request->user()->tokens()
            ->orderByDesc('created_at')
            ->get();

        $tokenIds = $tokens->pluck('id');
        $useCounts = ApiTokenUse::whereIn('token_id', $tokenIds)
            ->groupBy('token_id')
            ->selectRaw('token_id, count(*) as count')
            ->pluck('count', 'token_id');

        $tokens = $tokens->map(fn (PersonalAccessToken $token) => [
            'id' => $token->id,
            'name' => $token->name,
            'abilities' => $token->abilities,
            'last_used_at' => $token->last_used_at?->toIso8601String(),
            'created_at' => $token->created_at->toIso8601String(),
            'uses_count' => $useCounts[$token->id] ?? 0,
        ]);

        return Inertia::render('settings/ApiKeys', [
            'tokens' => $tokens,
            'availableScopes' => [
                'read',
                'write',
                'prompts:read',
                'prompts:write',
                'runs:read',
                'runs:write',
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'abilities' => ['required', 'array', 'min:1'],
            'abilities.*' => ['string', Rule::in([
                'read', 'write',
                'prompts:read', 'prompts:write',
                'runs:read', 'runs:write',
            ])],
        ]);

        $token = $request->user()->createToken($validated['name'], $validated['abilities']);

        return back()->with('success', __('messages.apiKeyCreated'))->with('created_token', $token->plainTextToken);
    }

    public function destroy(Request $request, int $tokenId): RedirectResponse
    {
        $token = $request->user()->tokens()->findOrFail($tokenId);
        $token->delete();

        return back()->with('success', __('messages.apiKeyDeleted'));
    }
}
