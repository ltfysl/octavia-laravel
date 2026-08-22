<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;

uses(RefreshDatabase::class);

it('applies security headers to web responses', function () {
    $response = $this->get('/');

    $response->assertHeader('X-Frame-Options', 'DENY');
    $response->assertHeader('X-Content-Type-Options', 'nosniff');
    $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
});

it('blocks login attempts after repeated failures from the same identity', function () {
    $user = User::factory()->create(['email' => 'victim@example.com']);

    // 5 allowed attempts per minute per email+IP.
    foreach (range(1, 5) as $i) {
        $this->post('/login', ['email' => 'victim@example.com', 'password' => 'wrong-'.$i])
            ->assertSessionHasErrors('email');
    }

    RateLimiter::clear('victim@example.com|127.0.0.1'); // ensure deterministic count
    foreach (range(1, 5) as $i) {
        $this->post('/login', ['email' => 'victim@example.com', 'password' => 'wrong-again-'.$i]);
    }

    // The next attempt within the window must be throttled (HTTP 429).
    $this->post('/login', ['email' => 'victim@example.com', 'password' => 'guess'])
        ->assertStatus(429);
});
