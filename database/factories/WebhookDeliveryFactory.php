<?php

namespace Database\Factories;

use App\Models\Webhook;
use App\Models\WebhookDelivery;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<WebhookDelivery> */
class WebhookDeliveryFactory extends Factory
{
    protected $model = WebhookDelivery::class;

    public function definition(): array
    {
        return [
            'webhook_id' => Webhook::factory(),
            'event' => fake()->randomElement(['run.completed', 'run.failed']),
            'payload' => ['id' => fake()->randomNumber()],
            'status' => fake()->randomElement(['pending', 'delivered', 'failed']),
            'response_code' => fake()->randomElement([200, 500]),
            'attempts' => 1,
            'delivered_at' => now(),
        ];
    }
}
