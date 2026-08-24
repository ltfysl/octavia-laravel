<?php

namespace Database\Factories;

use App\Models\PromptTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<PromptTemplate> */
class PromptTemplateFactory extends Factory
{
    protected $model = PromptTemplate::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'description' => fake()->sentence(),
            'category' => fake()->randomElement(['general', 'marketing', 'support', 'coding']),
            'difficulty' => fake()->randomElement(['beginner', 'medium', 'advanced']),
            'tags' => fake()->words(3, true),
            'body' => fake()->paragraph(3),
            'example_use_cases' => fake()->paragraph(),
            'recommended_benchmark_type' => null,
            'is_custom' => false,
        ];
    }
}
