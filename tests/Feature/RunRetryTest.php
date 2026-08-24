<?php

use App\Jobs\ProcessRunJob;
use App\Models\Prompt;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

uses()->group('web')->in(__DIR__);

test('restarting a finished run queues a new run with the same settings', function () {
    Queue::fake();
    $user = User::factory()->create(['credits_balance' => 100]);
    $prompt = Prompt::factory()->for($user)->withContent('Test.')->create();
    $run = $user->runs()->create([
        'prompt_id' => $prompt->id,
        'name' => 'Original run',
        'mode' => 'evaluate',
        'status' => 'completed',
        'provider' => 'mock',
        'max_steps' => 1,
        'target_score' => 0.95,
    ]);

    $this->actingAs($user)->post("/runs/{$run->id}/retry")
        ->assertRedirect();

    $newRun = $user->fresh()->runs()->latest('id')->first();
    expect($newRun->id)->not->toBe($run->id)
        ->and($newRun->prompt_id)->toBe($run->prompt_id)
        ->and($newRun->mode->value)->toBe($run->mode->value)
        ->and($newRun->status->value)->toBe('pending')
        ->and($newRun->name)->toContain('retry');

    Queue::assertPushed(ProcessRunJob::class);
});

test('active runs cannot be retried', function () {
    $user = User::factory()->create(['credits_balance' => 100]);
    $prompt = Prompt::factory()->for($user)->withContent('Test.')->create();
    $run = $user->runs()->create([
        'prompt_id' => $prompt->id,
        'name' => 'Running',
        'mode' => 'evaluate',
        'status' => 'running',
        'provider' => 'mock',
    ]);

    $this->actingAs($user)->post("/runs/{$run->id}/retry")
        ->assertRedirect()
        ->assertSessionHasErrors('run');
});

test('strangers cannot retry another user run', function () {
    $owner = User::factory()->create(['credits_balance' => 100]);
    $stranger = User::factory()->create(['credits_balance' => 100]);
    $prompt = Prompt::factory()->for($owner)->withContent('Test.')->create();
    $run = $owner->runs()->create([
        'prompt_id' => $prompt->id,
        'name' => 'Owner run',
        'mode' => 'evaluate',
        'status' => 'completed',
        'provider' => 'mock',
    ]);

    $this->actingAs($stranger)->post("/runs/{$run->id}/retry")
        ->assertNotFound();
});
