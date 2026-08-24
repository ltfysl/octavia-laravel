<?php

namespace Database\Factories;

use App\Models\BenchmarkCase;
use App\Models\BenchmarkCriterion;
use Illuminate\Database\Eloquent\Factories\Factory;

class BenchmarkCriterionFactory extends Factory
{
    protected $model = BenchmarkCriterion::class;

    public function definition(): array
    {
        return [
            'benchmark_case_id' => BenchmarkCase::factory(),
            'type' => 'contains',
            'label' => $this->faker->word(),
            'config' => ['needles' => [$this->faker->word()]],
        ];
    }
}
