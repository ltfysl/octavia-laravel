<?php

use App\Models\Benchmark;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;

uses(RefreshDatabase::class);

it('imports a benchmark from a valid JSON export', function () {
    $user = User::factory()->create();

    $json = json_encode([
        'name' => 'Imported Benchmark',
        'description' => 'From JSON',
        'category' => 'coding',
        'cases' => [
            [
                'title' => 'Case 1',
                'input' => 'Input 1',
                'weight' => 1.5,
                'criteria' => [
                    ['type' => 'contains', 'label' => 'Has keyword', 'config' => ['values' => ['keyword']]],
                    ['type' => 'regex', 'label' => 'Has digits', 'config' => ['pattern' => '/[0-9]/']],
                ],
            ],
        ],
    ]);

    $this->actingAs($user)->post('/benchmarks/import', [
        'file' => File::fake()->createWithContent('import.json', $json),
    ])->assertRedirect(route('benchmarks.show', Benchmark::first()));

    $benchmark = Benchmark::first();
    expect($benchmark->name)->toBe('Imported Benchmark')
        ->and($benchmark->cases()->count())->toBe(1)
        ->and($benchmark->cases()->first()->criteria()->count())->toBe(2)
        ->and((float) $benchmark->cases()->first()->weight)->toBe(1.5);
});

it('rejects invalid JSON', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/benchmarks/import', [
        'file' => File::fake()->createWithContent('bad.json', 'not json at all'),
    ])->assertRedirect();
});
