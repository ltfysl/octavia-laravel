<?php

namespace Database\Factories;

use App\Enums\BenchmarkCategory;
use App\Models\Benchmark;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Benchmark>
 */
class BenchmarkFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->unique()->words(3, true),
            'description' => fake()->sentence(),
            'category' => BenchmarkCategory::General->value,
            'visibility' => 'private',
        ];
    }

    public function category(BenchmarkCategory $category): static
    {
        return $this->state(fn () => ['category' => $category->value]);
    }

    public function public(): static
    {
        return $this->state(fn () => ['visibility' => 'public']);
    }

    /**
     * Benchmark with one case that has a single `contains` criterion.
     */
    public function withContainsCase(string $needle, string $input = 'Do the task.'): static
    {
        return $this->hasCasesWithCriteria([
            ['title' => 'Basic case', 'input' => $input, 'criteria' => [
                ['type' => 'contains', 'label' => "Mentions {$needle}", 'config' => ['values' => [$needle]]],
            ]],
        ]);
    }

    /**
     * @param  list<array{title: string, input: string, weight?: float, criteria: list<array{type: string, label: string, config: array}>}>  $cases
     */
    public function hasCasesWithCriteria(array $cases): static
    {
        return $this->afterCreating(function (Benchmark $benchmark) use ($cases) {
            foreach ($cases as $i => $caseData) {
                $criteria = $caseData['criteria'];
                unset($caseData['criteria']);

                $case = $benchmark->cases()->create(array_merge($caseData, ['position' => $i]));

                foreach ($criteria as $j => $criterion) {
                    $case->criteria()->create(array_merge($criterion, ['position' => $j]));
                }
            }
        });
    }
}
