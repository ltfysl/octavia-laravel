<?php

use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('finds prompts and benchmarks matching a query', function () {
    $user = User::factory()->create();

    Prompt::factory()->for($user)->withContent('Test.')->create(['name' => 'Laravel Expert']);
    Benchmark::factory()->for($user)->withContainsCase('x')->create(['name' => 'Laravel Quality']);

    Prompt::factory()->for($user)->withContent('Test.')->create(['name' => 'SEO Writer']);

    $this->actingAs($user)->get('/search?q=Laravel')->assertInertia(fn ($page) => $page
        ->where('query', 'Laravel')
        ->has('results.prompts', 1)
        ->has('results.benchmarks', 1));
});

it('requires authentication', function () {
    $this->get('/search?q=anything')->assertRedirect('/login');
});

it('returns empty results for a blank query', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/search?q=')
        ->assertInertia(fn ($page) => $page
            ->where('query', '')
            ->has('results.prompts', 0));
});
