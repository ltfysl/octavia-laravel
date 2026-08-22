<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('persists the notification preference from settings', function () {
    $user = User::factory()->create(['notify_run_completed_mail' => true]);

    $this->actingAs($user)->patch('/settings/profile', [
        'name' => $user->name,
        'locale' => 'de',
        'notify_run_completed_mail' => false,
    ])->assertRedirect();

    expect($user->fresh()->notify_run_completed_mail)->toBeFalse()
        ->and($user->fresh()->locale)->toBe('de');

    // Omitting the flag keeps the stored value (checkbox unchecked sends false,
    // so only absence preserves).
    $this->actingAs($user)->patch('/settings/profile', [
        'name' => 'Renamed',
        'locale' => 'en',
    ]);

    expect($user->fresh()->name)->toBe('Renamed');
});

it('rejects invalid preference values', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->patch('/settings/profile', [
        'name' => 'X',
        'locale' => 'fr',
        'notify_run_completed_mail' => 'maybe',
    ])->assertSessionHasErrors(['locale', 'notify_run_completed_mail']);
});
