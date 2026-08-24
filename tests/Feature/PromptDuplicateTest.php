<?php

use App\Models\Prompt;
use App\Models\User;

test('owner can duplicate a prompt with its current content', function () {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->create(['name' => 'Original']);
    $version = $prompt->versions()->create([
        'version' => 1,
        'content' => 'Original content',
        'changelog' => 'Initial',
    ]);
    $prompt->update(['current_version_id' => $version->id]);

    $this->actingAs($user)->post("/prompts/{$prompt->id}/duplicate")
        ->assertRedirect();

    $copy = $user->fresh()->prompts()->latest('id')->first();
    expect($copy->id)->not->toBe($prompt->id)
        ->and($copy->name)->toContain('copy')
        ->and($copy->visibility->value)->toBe('private')
        ->and($copy->currentContent())->toBe('Original content')
        ->and($copy->versions->first()->changelog)->toContain('Original');
});

test('strangers cannot duplicate a private prompt', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $prompt = Prompt::factory()->for($owner)->create(['visibility' => 'private']);

    $this->actingAs($stranger)->post("/prompts/{$prompt->id}/duplicate")
        ->assertNotFound();
});
