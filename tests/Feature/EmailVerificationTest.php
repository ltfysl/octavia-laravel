<?php

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

it('sends a verification link at registration', function () {
    Notification::fake();

    $this->post('/register', [
        'name' => 'Ada',
        'email' => 'ada@example.com',
        'password' => 'supersecret1',
        'password_confirmation' => 'supersecret1',
    ])->assertRedirect(route('onboarding.welcome'));

    Notification::assertSentTo(
        User::where('email', 'ada@example.com')->first(),
        VerifyEmailNotification::class,
    );
});

it('resends the verification link for unverified users', function () {
    $user = User::factory()->create(['email_verified_at' => null]);

    Notification::fake();

    $this->actingAs($user)->post('/email/verification-notification')
        ->assertRedirect()
        ->assertSessionHas('success');

    Notification::assertSentTo($user, VerifyEmailNotification::class);
});

it('throttles resend attempts', function () {
    $user = User::factory()->create(['email_verified_at' => null]);

    foreach (range(1, 6) as $i) {
        $this->actingAs($user)->post('/email/verification-notification');
    }

    $this->actingAs($user)->post('/email/verification-notification')
        ->assertStatus(429);
});

it('marks the email verified through the signed link', function () {
    $user = User::factory()->create(['email_verified_at' => null]);
    $user = $user->fresh();

    $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
        'id' => $user->id,
        'hash' => sha1($user->email),
    ]);

    $this->actingAs($user)->get($url)
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('success');

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

it('rejects unsigned or mismatched verification links', function () {
    $user = User::factory()->create(['email_verified_at' => null]);

    // Wrong hash
    $badHash = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
        'id' => $user->id,
        'hash' => sha1('someone-else@example.com'),
    ]);
    $this->actingAs($user)->get($badHash)->assertForbidden();

    // Signed correctly but for another user id
    $wrongId = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
        'id' => 999999,
        'hash' => sha1($user->email),
    ]);
    $this->actingAs($user)->get($wrongId)->assertForbidden();

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});
