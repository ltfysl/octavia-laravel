<?php

use App\Models\Prompt;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

it('registers a user and redirects to onboarding', function () {
    $response = $this->post('/register', [
        'name' => 'Ada',
        'email' => 'ada@example.com',
        'password' => 'supersecret1',
        'password_confirmation' => 'supersecret1',
    ]);

    $response->assertRedirect(route('onboarding.welcome'));
    $this->assertAuthenticated();
});

it('rejects duplicate email registration', function () {
    User::factory()->create(['email' => 'ada@example.com']);

    $this->from('/register')->post('/register', [
        'name' => 'Ada',
        'email' => 'ada@example.com',
        'password' => 'supersecret1',
        'password_confirmation' => 'supersecret1',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('logs a user in and out', function () {
    $user = User::factory()->create();

    $this->post('/login', ['email' => $user->email, 'password' => 'password'])
        ->assertRedirect(route('onboarding.welcome'));

    $this->post('/logout')->assertRedirect(route('home'));
    $this->assertGuest();
});

it('sends the password reset link without leaking account existence', function () {
    $this->post('/forgot-password', ['email' => 'nobody@example.com'])
        ->assertSessionHas('success');
});

it('redirects guests from app routes to login', function () {
    $this->get('/dashboard')->assertRedirect('/login');
    $this->get('/prompts')->assertRedirect('/login');
});

it('completes onboarding and seeds starter content', function () {
    $user = User::factory()->create(['onboarded_at' => null]);

    $this->actingAs($user)->post('/welcome/complete', [
        'locale' => 'de',
        'sample' => true,
    ])->assertRedirect(route('dashboard'));

    expect($user->fresh()->locale)->toBe('de')
        ->and($user->fresh()->onboarded_at)->not->toBeNull()
        ->and($user->prompts()->count())->toBe(1)
        ->and($user->benchmarks()->count())->toBe(1)
        ->and($user->benchmarks()->first()->cases()->count())->toBe(2);
});

it('keeps prompts isolated between users', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();

    $prompt = Prompt::factory()->for($owner)->withContent('Secret prompt.')->create();

    $this->actingAs($other)->get("/prompts/{$prompt->id}")->assertForbidden();
    $this->actingAs($other)->delete("/prompts/{$prompt->id}")->assertForbidden();

    $this->actingAs($owner)->get("/prompts/{$prompt->id}")->assertOk();
});

it('sends a localized welcome mail after onboarding', function () {
    Notification::fake();

    $user = User::factory()->create(['locale' => 'de', 'onboarded_at' => null]);

    $this->actingAs($user)->post('/welcome/complete', ['sample' => false]);

    Notification::assertSentTo($user, WelcomeNotification::class, function ($n, $ch) use ($user) {
        $mail = $n->toMail($user);

        return str_contains($mail->subject, 'Willkommen bei Octavia');
    });
});
