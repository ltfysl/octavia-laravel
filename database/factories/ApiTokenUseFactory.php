<?php

namespace Database\Factories;

use App\Models\ApiTokenUse;
use Illuminate\Database\Eloquent\Factories\Factory;
use Laravel\Sanctum\PersonalAccessToken;

/** @extends Factory<ApiTokenUse> */
class ApiTokenUseFactory extends Factory
{
    protected $model = ApiTokenUse::class;

    public function definition(): array
    {
        return [
            'token_id' => PersonalAccessToken::factory(),
            'method' => fake()->randomElement(['GET', 'POST']),
            'path' => '/api/v1/prompts',
            'status' => 200,
            'duration_ms' => fake()->numberBetween(10, 500),
            'tokens_used' => fake()->numberBetween(1, 5000),
            'ip' => fake()->ipv4(),
            'created_at' => now(),
        ];
    }
}
