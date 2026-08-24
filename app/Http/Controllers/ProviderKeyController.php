<?php

namespace App\Http\Controllers;

use App\Models\ProviderKey;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ProviderKeyController extends Controller
{
    public function index(Request $request): Response
    {
        $keys = $request->user()->providerKeys()
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (ProviderKey $key) => [
                'id' => $key->id,
                'provider' => $key->provider,
                'is_active' => $key->is_active,
                'last_used_at' => $key->last_used_at?->toIso8601String(),
                'created_at' => $key->created_at->toIso8601String(),
            ]);

        return Inertia::render('settings/ProviderKeys', [
            'keys' => $keys,
            'availableProviders' => array_keys(config('llm.providers', [])),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'provider' => ['required', 'string', Rule::in(array_keys(config('llm.providers', [])))],
            'api_key' => ['required', 'string', 'max:2048'],
        ]);

        $request->user()->providerKeys()->updateOrCreate(
            ['provider' => $validated['provider']],
            ['api_key' => $validated['api_key'], 'is_active' => true]
        );

        return back()->with('success', __('messages.providerKeySaved'));
    }

    public function update(Request $request, ProviderKey $providerKey): RedirectResponse
    {
        $this->authorize('update', $providerKey);

        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $providerKey->update($validated);

        return back()->with('success', __('messages.providerKeyUpdated'));
    }

    public function destroy(Request $request, ProviderKey $providerKey): RedirectResponse
    {
        $this->authorize('delete', $providerKey);

        $providerKey->delete();

        return back()->with('success', __('messages.providerKeyDeleted'));
    }
}
