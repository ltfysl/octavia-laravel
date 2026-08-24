<?php

use App\Models\Prompt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('runs prompt playground chat with message history', function (): void {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->create();
    $prompt->versions()->create([
        'version' => 1,
        'content' => 'You are a marketing assistant.',
        'created_by' => $user->id,
    ]);

    $this->actingAs($user)
        ->postJson("/prompts/{$prompt->id}/playground/chat", [
            'messages' => [
                ['role' => 'user', 'content' => 'Product: solar lamp'],
            ],
        ])
        ->assertOk()
        ->assertJsonStructure(['output'])
        ->assertJsonPath('output', 'Product: solar lamp');
});
