<?php

use App\Models\Prompt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('reviews the current prompt via the AI insight endpoint', function (): void {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->withContent('Write a short tagline. Requirements: under 10 words.')->create();

    $response = $this->actingAs($user)->postJson("/prompts/{$prompt->id}/insight");

    $response->assertOk()
        ->assertJsonStructure(['insight', 'tokens']);

    expect($response->json('insight'))->toContain('Structure');
});

it('denies insight for other users', function (): void {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $prompt = Prompt::factory()->for($owner)->withContent('Test.')->create();

    $this->actingAs($stranger)->postJson("/prompts/{$prompt->id}/insight")
        ->assertStatus(404);
});
