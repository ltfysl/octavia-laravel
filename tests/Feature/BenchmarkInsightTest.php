<?php

use App\Models\Benchmark;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('reviews a benchmark via the AI insight endpoint', function (): void {
    $user = User::factory()->create();
    $benchmark = Benchmark::factory()->for($user)->withContainsCase('EcoSip', 'Eco bottle brand EcoSip')->create();

    $response = $this->actingAs($user)->postJson("/benchmarks/{$benchmark->id}/insight");

    $response->assertOk()
        ->assertJsonStructure(['insight', 'tokens']);

    expect($response->json('insight'))->toContain('Structure');
});

it('denies insight for other users', function (): void {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $benchmark = Benchmark::factory()->for($owner)->withContainsCase('x')->create();

    $this->actingAs($stranger)->postJson("/benchmarks/{$benchmark->id}/insight")
        ->assertStatus(404);
});
