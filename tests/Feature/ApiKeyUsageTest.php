<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lists api keys for the user', function (): void {
    $user = User::factory()->create();
    $user->createToken('CLI', ['read']);

    $this->actingAs($user)->get('/settings/api-keys')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('tokens'));
});

it('creates an api key', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/settings/api-keys', [
        'name' => 'CLI',
        'abilities' => ['read'],
    ])->assertRedirect();

    $this->assertDatabaseHas('personal_access_tokens', [
        'tokenable_id' => $user->id,
        'name' => 'CLI',
    ]);
});

it('deletes an api key', function (): void {
    $user = User::factory()->create();
    $token = $user->createToken('CLI', ['read'])->accessToken;

    $this->actingAs($user)->delete("/settings/api-keys/{$token->id}")
        ->assertRedirect();

    $this->assertDatabaseMissing('personal_access_tokens', ['id' => $token->id]);
});

it('logs api calls for token-authenticated requests', function (): void {
    $user = User::factory()->create();
    $token = $user->createToken('CLI', ['prompts:read'])->plainTextToken;

    $this->withHeaders(['Authorization' => 'Bearer '.$token, 'Accept' => 'application/json'])
        ->getJson('/api/v1/prompts')
        ->assertOk();

    $this->assertDatabaseHas('api_token_uses', [
        'method' => 'GET',
        'path' => '/api/v1/prompts',
        'status' => 200,
    ]);
});
