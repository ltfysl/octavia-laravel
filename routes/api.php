<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Api\AuthTokenController;
use App\Http\Controllers\Api\PromptController;
use App\Http\Controllers\Api\RunController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API v1
|--------------------------------------------------------------------------
| Token-authenticated (Sanctum). Obtain a token via POST /api/v1/auth/token,
| then send `Authorization: Bearer <token>`. All endpoints are rate limited.
|
| Scopes: tokens may carry fine-grained scopes (prompts:read, prompts:write,
| runs:read, runs:write) or the legacy coarse abilities (read, write —
| write implies read). The `scope` middleware accepts either form.
*/

Route::prefix('v1')->group(function () {
    Route::post('/auth/token', [AuthTokenController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('api.auth.token');

    Route::middleware(['auth:sanctum', 'log.api.usage'])->group(function () {
        Route::delete('/auth/token', [AuthTokenController::class, 'destroy'])->name('api.auth.logout');
        Route::get('/activity', [ActivityController::class, 'api'])->middleware('scope:runs:read')->name('api.activity');

        Route::get('/me', fn (Request $request) => $request->user()->only(['id', 'name', 'email']))
            ->name('api.me');

        Route::get('/prompts', [PromptController::class, 'index'])
            ->middleware('scope:prompts:read')->name('api.prompts.index');
        Route::post('/prompts', [PromptController::class, 'store'])
            ->middleware('scope:prompts:write')->name('api.prompts.store');
        Route::get('/prompts/{prompt}', [PromptController::class, 'show'])
            ->middleware('scope:prompts:read')->name('api.prompts.show');
        Route::get('/prompts/{prompt}/diff', [PromptController::class, 'diff'])
            ->middleware('scope:prompts:read')->name('api.prompts.diff');
        Route::post('/prompts/{prompt}/evaluate', [PromptController::class, 'evaluate'])
            ->middleware(['scope:prompts:read', 'throttle:30,1'])
            ->name('api.prompts.evaluate');

        Route::post('/runs', [RunController::class, 'store'])
            ->middleware(['scope:runs:write', 'throttle:runs'])
            ->name('api.runs.store');
        Route::get('/runs/{run}', [RunController::class, 'show'])
            ->middleware('scope:runs:read')->name('api.runs.show');
        Route::post('/runs/{run}/cancel', [RunController::class, 'cancel'])
            ->middleware('scope:runs:write')->name('api.runs.cancel');
    });
});
