<?php

namespace Database\Factories;

use App\Models\Benchmark;
use App\Models\BenchmarkCase;
use Illuminate\Database\Eloquent\Factories\Factory;

class BenchmarkCaseFactory extends Factory
{
    protected $model = BenchmarkCase::class;

    public function definition(): array
    {
        return [
            'benchmark_id' => Benchmark::factory(),
            'title' => $this->faker->sentence(),
            'input' => $this->faker->paragraph(),
        ];
    }
}
