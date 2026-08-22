<?php

use App\Models\Prompt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createVersionedPrompt(User $user, array $contents): Prompt
{
    $prompt = $user->prompts()->create(['name' => 'Versioned']);

    foreach ($contents as $i => $content) {
        $version = $prompt->versions()->create([
            'version' => $i + 1,
            'content' => $content,
            'created_at' => now(),
        ]);
        $prompt->update(['current_version_id' => $version->id]);
    }

    return $prompt->fresh();
}

it('returns the diff source between the last two versions by default', function () {
    $user = User::factory()->create();
    $prompt = createVersionedPrompt($user, ['One', 'Two', 'Three']);

    $this->actingAs($user, 'sanctum')
        ->getJson("/api/v1/prompts/{$prompt->id}/diff")
        ->assertOk()
        ->assertJsonPath('from.version', 2)
        ->assertJsonPath('to.version', 3)
        ->assertJsonPath('from.content', 'Two');
});

it('accepts explicit from and to versions', function () {
    $user = User::factory()->create();
    $prompt = createVersionedPrompt($user, ['One', 'Two', 'Three']);

    $this->actingAs($user, 'sanctum')
        ->getJson("/api/v1/prompts/{$prompt->id}/diff?from=1&to=3")
        ->assertOk()
        ->assertJsonPath('from.version', 1)
        ->assertJsonPath('to.version', 3)
        ->assertJsonPath('to.content', 'Three');
});

it('rejects single-version prompts and foreign prompts', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $single = createVersionedPrompt($user, ['Only']);
    $foreign = createVersionedPrompt($other, ['A', 'B']);

    $this->actingAs($user, 'sanctum')
        ->getJson("/api/v1/prompts/{$single->id}/diff")
        ->assertStatus(422);

    $this->actingAs($user, 'sanctum')
        ->getJson("/api/v1/prompts/{$foreign->id}/diff")
        ->assertNotFound();
});
