<?php

namespace App\Actions;

use App\Enums\BenchmarkCategory;
use App\Enums\CriterionType;
use App\Models\Benchmark;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ImportBenchmark
{
    /**
     * Creates a benchmark from a parsed JSON payload (exported via
     * BenchmarkExportController). Validates structure and creates
     * cases + criteria in a transaction.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(User $user, array $data): Benchmark
    {
        $required = ['name', 'category', 'cases'];
        foreach ($required as $key) {
            if (! isset($data[$key])) {
                throw ValidationException::withMessages([$key => "Missing required field: {$key}"]);
            }
        }

        if (! in_array($data['category'], array_column(BenchmarkCategory::cases(), 'value'))) {
            throw ValidationException::withMessages(['category' => 'Invalid category.']);
        }

        return DB::transaction(function () use ($user, $data) {
            $benchmark = $user->benchmarks()->create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'category' => $data['category'],
                'visibility' => 'private',
            ]);

            foreach ($data['cases'] as $i => $caseData) {
                $case = $benchmark->cases()->create([
                    'title' => $caseData['title'],
                    'input' => $caseData['input'],
                    'weight' => $caseData['weight'] ?? 1,
                    'position' => $i,
                ]);

                foreach ($caseData['criteria'] as $j => $criterion) {
                    if (! in_array($criterion['type'], array_column(CriterionType::cases(), 'value'))) {
                        continue;
                    }

                    $case->criteria()->create([
                        'type' => $criterion['type'],
                        'label' => $criterion['label'],
                        'config' => $criterion['config'],
                        'position' => $j,
                    ]);
                }
            }

            return $benchmark;
        });
    }
}
