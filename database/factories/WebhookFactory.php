<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Webhook;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Webhook> */
class WebhookFactory extends Factory
{
    protected $model = Webhook::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'url' => fake()->url(),
            'secret' => bin2hex(random_bytes(16)),
            'events' => ['run.completed', 'run.failed'],
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
