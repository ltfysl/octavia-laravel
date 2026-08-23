<?php

namespace Database\Factories;

use App\Models\ConfigPreset;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConfigPresetFactory extends Factory
{
    protected $model = ConfigPreset::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'config' => [
                'mode' => 'optimize',
                'provider' => 'mock',
                'model' => 'default',
                'max_steps' => 8,
                'target_score' => 0.95,
                'temperature' => 0.7,
            ],
            'is_default' => false,
        ];
    }
}
