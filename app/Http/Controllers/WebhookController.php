<?php

namespace App\Http\Controllers;

use App\Models\Webhook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class WebhookController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('settings/Webhooks', [
            'webhooks' => $request->user()
                ->webhooks()
                ->withCount('deliveries')
                ->orderByDesc('created_at')
                ->get()
                ->map(fn (Webhook $webhook) => [
                    'id' => $webhook->id,
                    'url' => $webhook->url,
                    'description' => $webhook->description,
                    'events' => $webhook->events,
                    'is_active' => $webhook->is_active,
                    'deliveries_count' => $webhook->deliveries_count,
                    'created_at' => $webhook->created_at->toIso8601String(),
                ]),
            'events' => Webhook::EVENTS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'url' => ['required', 'url', 'max:2048'],
            'description' => ['nullable', 'string', 'max:1000'],
            'events' => ['required', 'array', 'min:1'],
            'events.*' => ['required', 'string', Rule::in(Webhook::EVENTS)],
        ]);

        $request->user()->webhooks()->create([
            'url' => $validated['url'],
            'description' => $validated['description'] ?? null,
            'events' => $validated['events'],
            'secret' => bin2hex(random_bytes(16)),
        ]);

        return back()->with('success', __('Webhook created.'));
    }

    public function update(Request $request, Webhook $webhook): RedirectResponse
    {
        $this->authorize('update', $webhook);

        $validated = $request->validate([
            'url' => ['required', 'url', 'max:2048'],
            'description' => ['nullable', 'string', 'max:1000'],
            'events' => ['required', 'array', 'min:1'],
            'events.*' => ['required', 'string', Rule::in(Webhook::EVENTS)],
            'is_active' => ['required', 'boolean'],
        ]);

        $webhook->update($validated);

        return back()->with('success', __('Webhook updated.'));
    }

    public function destroy(Webhook $webhook): RedirectResponse
    {
        $this->authorize('delete', $webhook);

        $webhook->delete();

        return back()->with('success', __('Webhook deleted.'));
    }

    public function deliveries(Request $request, Webhook $webhook): JsonResponse
    {
        $this->authorize('view', $webhook);

        $deliveries = $webhook->deliveries()
            ->latest()
            ->limit(30)
            ->get(['id', 'event', 'status', 'response_code', 'attempts', 'delivered_at', 'created_at'])
            ->map(fn ($delivery) => [
                'id' => $delivery->id,
                'event' => $delivery->event,
                'status' => $delivery->status,
                'response_code' => $delivery->response_code,
                'attempts' => $delivery->attempts,
                'delivered_at' => $delivery->delivered_at?->toIso8601String(),
                'created_at' => $delivery->created_at->toIso8601String(),
            ]);

        return response()->json($deliveries);
    }
}
