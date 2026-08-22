<?php

use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\User;
use App\Notifications\RunCompletedNotification;
use App\Services\EvolutionService;
use App\Services\Llm\LlmManager;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lands a database notification that the bell can read', function () {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->withContent('Test.')->create();
    $benchmark = Benchmark::factory()->for($user)->withContainsCase('x')->create();

    $run = $user->runs()->create([
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'name' => 'Bell run',
        'mode' => 'optimize',
        'status' => 'pending',
        'provider' => 'mock',
    ]);

    app(EvolutionService::class)->run($run, app(LlmManager::class)->provider('mock'));

    expect($user->unreadNotifications()->count())->toBe(1);

    $notification = $user->unreadNotifications->first();
    expect(data_get($notification, 'data.run_name'))->toBe('Bell run')
        ->and(data_get($notification, 'data.status'))->toBe('completed');

    // Marking read empties the unread feed.
    $user->unreadNotifications->markAsRead();
    expect($user->fresh()->unreadNotifications()->count())->toBe(0);
});

it('includes the mail channel by default', function () {
    $user = User::factory()->create(['notify_run_completed_mail' => true]);
    $prompt = Prompt::factory()->for($user)->withContent('Test.')->create();

    $run = $user->runs()->make([
        'name' => 'R',
        'mode' => 'optimize',
        'status' => 'pending',
        'provider' => 'mock',
    ]);
    $run->prompt_id = $prompt->id;

    expect((new RunCompletedNotification($run))->via($user))->toContain('mail');
});

it('omits the mail channel for opted-out users', function () {
    $optedOut = User::factory()->create(['notify_run_completed_mail' => false]);
    $prompt = Prompt::factory()->for($optedOut)->withContent('Test.')->create();

    $run = $optedOut->runs()->make([
        'name' => 'Silent run',
        'mode' => 'optimize',
        'status' => 'pending',
        'provider' => 'mock',
    ]);
    $run->prompt_id = $prompt->id;

    expect((new RunCompletedNotification($run))->via($optedOut))->toBe(['database']);
});
