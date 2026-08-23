<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('answers assistant chat via the configured provider', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/assistant/chat', [
        'messages' => [
            ['role' => 'system', 'content' => 'You are the Octavia prompt-lab assistant.'],
            ['role' => 'user', 'content' => 'How do benchmarks work?'],
        ],
    ]);

    $response->assertOk()
        ->assertJsonStructure(['reply', 'tokens']);

    expect($response->json('reply'))->not->toBe('');
});

it('rejects invalid roles and oversized threads', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->postJson('/assistant/chat', [
        'messages' => [['role' => 'tool', 'content' => 'x']],
    ])->assertStatus(422);

    $messages = [];
    for ($i = 0; $i < 21; $i++) {
        $messages[] = ['role' => 'user', 'content' => 'msg '.$i];
    }
    $this->actingAs($user)->postJson('/assistant/chat', ['messages' => $messages])
        ->assertStatus(422);
});

it('requires authentication and is throttled per minute', function (): void {
    $this->postJson('/assistant/chat', ['messages' => []])->assertStatus(401);

    config()->set('llm.default', 'mock');
    $user = User::factory()->create();
    $payload = ['messages' => [['role' => 'user', 'content' => 'hi']]];

    for ($i = 0; $i < 10; $i++) {
        $this->actingAs($user)->postJson('/assistant/chat', $payload)->assertOk();
    }
    $this->actingAs($user)->postJson('/assistant/chat', $payload)->assertStatus(429);
});
