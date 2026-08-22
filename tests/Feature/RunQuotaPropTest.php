<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shares the run quota prop with authenticated users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/dashboard')->assertInertia(fn ($page) => $page
        ->has('runQuota')
        ->where('runQuota.used', 0)
        ->where('runQuota.limit', config('llm.run_quota_per_day', 50)));
});

it('reflects consumed quota in the shared prop', function () {
    $user = User::factory()->create();

    // Consume quota directly through the rate limiter.
    for ($i = 0; $i < 45; $i++) {
        RateLimiter::hit('run:'.$user->id, 86400);
    }

    $this->actingAs($user)->get('/dashboard')->assertInertia(fn ($page) => $page
        ->has('runQuota')
        ->where('runQuota.used', 45));
});
