<?php

namespace Database\Factories;

use App\Models\ProviderKey;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ProviderKey> */
class ProviderKeyFactory extends Factory
{
    protected $model = ProviderKey::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'provider' => 'openai',
            'api_key' => fake()->regexify('[a-zA-Z0-9]{40}'),
            'is_active' => true,
        ];
    }
}
