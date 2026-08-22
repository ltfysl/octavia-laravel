<?php

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

it('sends a localized reset mail with a valid reset link', function () {
    Notification::fake();

    $user = User::factory()->create(['locale' => 'de']);

    $this->post('/forgot-password', ['email' => $user->email])
        ->assertRedirect()
        ->assertSessionHas('success');

    Notification::assertSentTo($user, ResetPasswordNotification::class, function (ResetPasswordNotification $notification, array $channels) use ($user) {
        $mail = $notification->toMail($user);

        // German subject (locale-aware) + the token-bearing action URL
        return str_contains($mail->subject, 'Setze dein Passwort zurück')
            && str_contains($mail->actionUrl, '/reset-password/')
            && str_contains($mail->actionUrl, 'email='.urlencode($user->email));
    });
});

it('keeps the reset flow working end to end with the custom notification', function () {
    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    $token = app('auth.password')->broker()->createToken($user);

    $this->post('/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'new-super-secret',
        'password_confirmation' => 'new-super-secret',
    ])->assertRedirect(route('login'));

    // Old password no longer works, new one does.
    $this->post('/login', ['email' => $user->email, 'password' => 'old-password'])
        ->assertSessionHasErrors();

    $this->post('/login', ['email' => $user->email, 'password' => 'new-super-secret'])
        ->assertRedirect(route('onboarding.welcome'));
});
