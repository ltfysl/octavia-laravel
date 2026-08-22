<?php

use App\Models\Prompt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('includes runs in search results', function () {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->withContent('Test.')->create();
    $run = $user->runs()->create([
        'prompt_id' => $prompt->id,
        'name' => 'Laravel optimization',
        'mode' => 'evaluate',
        'status' => 'completed',
        'provider' => 'mock',
    ]);

    $this->actingAs($user)->get('/search?q=Laravel')
        ->assertInertia(fn ($page) => $page
            ->has('results.runs', 1));
});

it('does not show other users runs in search', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $otherPrompt = Prompt::factory()->for($other)->withContent('Test.')->create();
    $other->runs()->create([
        'prompt_id' => $otherPrompt->id,
        'name' => 'Other user run',
        'mode' => 'optimize',
        'status' => 'completed',
        'provider' => 'mock',
    ]);

    $this->actingAs($user)->get('/search?q=Other+user+run')
        ->assertInertia(fn ($page) => $page
            ->has('results.runs', 0));
});
