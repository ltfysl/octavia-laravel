<?php

namespace App\Actions;

use App\Jobs\DeliverWebhook;
use App\Models\Run;
use App\Models\Webhook;

class DispatchRunWebhooks
{
    /**
     * Create webhook deliveries for all active webhooks subscribed to the run's event.
     */
    public function __invoke(Run $run, string $event): void
    {
        $payload = [
            'event' => $event,
            'run' => [
                'id' => $run->id,
                'name' => $run->name,
                'status' => $run->status->value,
                'mode' => $run->mode->value,
                'score' => $run->best_score,
                'provider' => $run->provider,
                'model' => $run->model,
                'created_at' => $run->created_at->toIso8601String(),
                'updated_at' => $run->updated_at->toIso8601String(),
            ],
        ];

        Webhook::where('user_id', $run->user_id)
            ->where('is_active', true)
            ->get()
            ->each(function (Webhook $webhook) use ($event, $payload) {
                if (! $webhook->subscribesTo($event)) {
                    return;
                }

                $delivery = $webhook->deliveries()->create([
                    'event' => $event,
                    'payload' => $payload,
                    'status' => 'pending',
                    'attempts' => 0,
                ]);

                DeliverWebhook::dispatch($delivery->id);
            });
    }
}
