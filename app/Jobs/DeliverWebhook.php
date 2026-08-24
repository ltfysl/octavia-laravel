<?php

namespace App\Jobs;

use App\Models\WebhookDelivery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeliverWebhook implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public array $backoff = [10, 60];

    public function __construct(public readonly int $deliveryId) {}

    public function handle(): void
    {
        $delivery = WebhookDelivery::with('webhook')->find($this->deliveryId);

        if (! $delivery || $delivery->status === 'delivered') {
            return;
        }

        $webhook = $delivery->webhook;

        if (! $webhook || ! $webhook->is_active) {
            $delivery->update(['status' => 'failed']);

            return;
        }

        $payload = json_encode($delivery->payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $signature = hash_hmac('sha256', (string) $payload, $webhook->secret);

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-Octavia-Event' => $delivery->event,
                    'X-Octavia-Signature' => 'sha256='.$signature,
                ])
                ->post($webhook->url, $delivery->payload);

            $delivery->update([
                'status' => $response->successful() ? 'delivered' : 'failed',
                'response_code' => $response->status(),
                'attempts' => $delivery->attempts + 1,
                'delivered_at' => $response->successful() ? now() : null,
            ]);

            if (! $response->successful()) {
                $this->release($this->backoff[min($delivery->attempts - 1, count($this->backoff) - 1)] ?? 60);
            }
        } catch (\Throwable $e) {
            $delivery->update([
                'status' => 'failed',
                'attempts' => $delivery->attempts + 1,
            ]);

            Log::warning('Webhook delivery failed', [
                'delivery_id' => $delivery->id,
                'error' => $e->getMessage(),
            ]);

            $this->release($this->backoff[min($delivery->attempts - 1, count($this->backoff) - 1)] ?? 60);
        }
    }
}
