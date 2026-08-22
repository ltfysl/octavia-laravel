<?php

use App\Jobs\ProcessRunJob;
use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\User;
use App\Notifications\RunCompletedNotification;
use App\Services\EvolutionService;
use App\Services\Llm\LlmManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

it('sends a localized completion mail when an optimize run finishes', function () {
    Notification::fake();

    $user = User::factory()->create(['locale' => 'de']);
    $prompt = Prompt::factory()->for($user)->withContent('Test.')->create();
    $benchmark = Benchmark::factory()->for($user)->withContainsCase('x')->create();

    $run = $user->runs()->create([
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'name' => 'Mail run',
        'mode' => 'optimize',
        'status' => 'pending',
        'provider' => 'mock',
    ]);

    app(EvolutionService::class)->run($run, app(LlmManager::class)->provider('mock'));

    expect($run->fresh()->status->value)->toBe('completed');

    Notification::assertSentTo($user, RunCompletedNotification::class, function (RunCompletedNotification $notification, array $channels) use ($user) {
        $mail = $notification->toMail($user);

        return in_array('mail', $channels)
            && str_contains($mail->subject, 'Lauf abgeschlossen');
    });
});

it('does not notify for evaluate-only runs', function () {
    Notification::fake();

    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->withContent('Test.')->create();
    $benchmark = Benchmark::factory()->for($user)->withContainsCase('x')->create();

    $run = $user->runs()->create([
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'name' => 'Quick eval',
        'mode' => 'evaluate',
        'status' => 'pending',
        'provider' => 'mock',
    ]);

    ProcessRunJob::dispatch($run->id);

    Notification::assertNothingSent();
});
