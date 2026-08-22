<?php

use App\Models\Prompt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('runs the playground without persisting anything', function () {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->withContent("You are a helper.\n- Always mention coffee")->create();

    $response = $this->actingAs($user)->postJson("/prompts/{$prompt->id}/playground", [
        'input' => 'Say hi.',
    ]);

    $response->assertOk()->assertJsonStructure(['output']);
    expect($response->json('output'))->toContain('coffee');

    expect($prompt->versions()->count())->toBe(1)
        ->and($prompt->runs()->count())->toBe(0);
});

it('supports testing unsaved content overrides', function () {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->withContent('Original.')->create();

    $this->actingAs($user)->postJson("/prompts/{$prompt->id}/playground", [
        'input' => 'Hello',
        'content' => "- Include the word banana\nYou are a fruit expert.",
    ])->assertOk();

    expect($prompt->fresh()->currentVersion->content)->toBe('Original.');
});

it('rejects guests and validates input', function () {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->withContent('x')->create();

    $this->postJson("/prompts/{$prompt->id}/playground", ['input' => 'hi'])->assertUnauthorized();

    $this->actingAs($user)->postJson("/prompts/{$prompt->id}/playground", [])->assertJsonValidationErrorFor('input');
});
