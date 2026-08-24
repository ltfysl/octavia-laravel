<?php

use App\Models\Prompt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('explains differences between two prompt versions', function (): void {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->create();
    $v1 = $prompt->versions()->create(['version' => 1, 'content' => 'You are a helpful assistant.', 'created_by' => $user->id]);
    $v2 = $prompt->versions()->create(['version' => 2, 'content' => 'You are a helpful assistant. Reply in JSON.', 'created_by' => $user->id]);

    $this->actingAs($user)->postJson("/prompts/{$prompt->id}/diff-explain", [
        'from_version_id' => $v1->id,
        'to_version_id' => $v2->id,
    ])
        ->assertOk()
        ->assertJsonStructure(['summary', 'changes', 'recommendation']);
});
