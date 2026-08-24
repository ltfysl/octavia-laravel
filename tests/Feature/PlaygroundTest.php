<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the standalone playground page', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/playground')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('playground/Index'));
});

it('returns an assistant response for playground chat', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->postJson('/playground/chat', [
        'message' => 'Hello',
        'systemPrompt' => 'You are a test assistant.',
    ])
        ->assertOk()
        ->assertJsonStructure(['role', 'content']);
});
