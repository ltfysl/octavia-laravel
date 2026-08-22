<?php

use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Email verification
|--------------------------------------------------------------------------
| Soft requirement: the app works unverified, but a persistent banner and
| resend endpoint nudge users toward verification.
*/

Route::middleware('auth')->group(function () {
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, '__invoke'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
});
