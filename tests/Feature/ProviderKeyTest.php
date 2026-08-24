<?php

use App\Models\ProviderKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lists provider keys for the user', function (): void {
    $user = User::factory()->create();
    ProviderKey::factory()->for($user)->create(['provider' => 'openai']);

    $this->actingAs($user)->get('/settings/provider-keys')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('keys'));
});

it('stores a provider key', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/settings/provider-keys', [
        'provider' => 'openai',
        'api_key' => 'sk-test',
    ])->assertRedirect();

    $this->assertDatabaseHas('provider_keys', [
        'user_id' => $user->id,
        'provider' => 'openai',
    ]);
});

it('updates a provider key active state', function (): void {
    $user = User::factory()->create();
    $key = ProviderKey::factory()->for($user)->create(['is_active' => true]);

    $this->actingAs($user)->patch("/settings/provider-keys/{$key->id}", ['is_active' => false])
        ->assertRedirect();

    $this->assertDatabaseHas('provider_keys', ['id' => $key->id, 'is_active' => 0]);
});

it('deletes a provider key', function (): void {
    $user = User::factory()->create();
    $key = ProviderKey::factory()->for($user)->create();

    $this->actingAs($user)->delete("/settings/provider-keys/{$key->id}")
        ->assertRedirect();

    $this->assertDatabaseMissing('provider_keys', ['id' => $key->id]);
});
