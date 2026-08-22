<?php

namespace Database\Factories;

use App\Models\Prompt;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prompt>
 */
class PromptFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->unique()->words(3, true),
            'description' => fake()->sentence(),
            'visibility' => 'private',
        ];
    }

    public function public(): static
    {
        return $this->state(fn () => ['visibility' => 'public']);
    }

    /**
     * Creates the prompt together with its initial version.
     */
    public function withContent(string $content): static
    {
        return $this->afterCreating(function (Prompt $prompt) use ($content) {
            $version = $prompt->versions()->create([
                'version' => 1,
                'content' => $content,
                'created_at' => now(),
            ]);
            $prompt->update(['current_version_id' => $version->id]);
        });
    }
}
