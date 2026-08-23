<?php

use App\Models\ConfigPreset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lists presets for the authenticated user', function (): void {
    $user = User::factory()->create();
    ConfigPreset::factory()->for($user)->count(3)->create();

    $response = $this->actingAs($user)->get('/settings/presets');

    $response->assertOk();
    expect($response->inertiaProps('presets'))->toHaveCount(3);
});

it('creates a preset', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/settings/presets', [
        'name' => 'Fast optimize',
        'description' => 'Low step budget',
        'config' => [
            'mode' => 'optimize',
            'provider' => 'mock',
            'model' => '',
            'max_steps' => 4,
            'target_score' => 0.8,
            'temperature' => 0.5,
        ],
        'is_default' => true,
    ]);

    $response->assertRedirect('/settings/presets');
    $this->assertDatabaseHas('config_presets', ['name' => 'Fast optimize', 'user_id' => $user->id, 'is_default' => 1]);
});

it('updates a preset', function (): void {
    $user = User::factory()->create();
    $preset = ConfigPreset::factory()->for($user)->create(['name' => 'Old']);

    $response = $this->actingAs($user)->patch("/settings/presets/{$preset->id}", [
        'name' => 'Updated',
        'description' => 'Updated desc',
        'config' => [
            'mode' => 'evaluate',
            'provider' => 'mock',
            'model' => '',
            'max_steps' => 4,
            'target_score' => 0.8,
            'temperature' => 0.5,
        ],
        'is_default' => false,
    ]);

    $response->assertRedirect('/settings/presets');
    $this->assertDatabaseHas('config_presets', ['id' => $preset->id, 'name' => 'Updated']);
});

it('prevents non-owners from updating or deleting presets', function (): void {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $preset = ConfigPreset::factory()->for($owner)->create();

    $this->actingAs($stranger)->patch("/settings/presets/{$preset->id}", [
        'name' => 'Hacked',
        'config' => [
            'mode' => 'evaluate',
            'provider' => 'mock',
            'model' => '',
            'max_steps' => 4,
            'target_score' => 0.8,
            'temperature' => 0.5,
        ],
        'is_default' => false,
    ])->assertStatus(404);

    $this->actingAs($stranger)->delete("/settings/presets/{$preset->id}")
        ->assertStatus(404);
});

it('deletes a preset', function (): void {
    $user = User::factory()->create();
    $preset = ConfigPreset::factory()->for($user)->create();

    $this->actingAs($user)->delete("/settings/presets/{$preset->id}")
        ->assertRedirect('/settings/presets');

    $this->assertDatabaseMissing('config_presets', ['id' => $preset->id]);
});
