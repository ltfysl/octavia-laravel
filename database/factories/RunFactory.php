<?php

namespace Database\Factories;

use App\Enums\RunMode;
use App\Enums\RunStatus;
use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\Run;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RunFactory extends Factory
{
    protected $model = Run::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'prompt_id' => Prompt::factory(),
            'benchmark_id' => Benchmark::factory(),
            'name' => 'Run '.$this->faker->word(),
            'mode' => RunMode::Evaluate,
            'status' => RunStatus::Pending,
            'provider' => 'mock',
            'max_steps' => 8,
            'target_score' => 0.95,
            'cost_optimized' => false,
        ];
    }
}
