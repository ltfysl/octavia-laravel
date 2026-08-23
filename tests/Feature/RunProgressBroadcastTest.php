<?php

use App\Broadcasting\AuthorizeRunChannel;
use App\Events\RunProgress;
use App\Jobs\ProcessRunJob;
use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('authorizes the run channel for the owner and rejects strangers', function () {
    $user = User::factory()->create();
    $stranger = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->withContent('x')->create();
    $benchmark = Benchmark::factory()->for($user)->withContainsCase('hi')->create();
    $run = $user->runs()->create([
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'name' => 'R',
        'mode' => 'evaluate',
        'status' => 'completed',
        'provider' => 'mock',
        'max_steps' => 1,
        'target_score' => 0.95,
    ]);

    $authorize = new AuthorizeRunChannel;

    expect($authorize($user, $run->id))->toBeTrue()
        ->and($authorize($stranger, $run->id))->toBeFalse();
});

it('broadcasts progress while an evaluate run executes', function () {
    Event::fake([RunProgress::class]);
    Queue::fake();

    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->withContent('Say hi.')->create();
    $benchmark = Benchmark::factory()->for($user)->withContainsCase('hi')->create();

    $this->actingAs($user)->post('/runs', [
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'mode' => 'evaluate',
    ]);

    Queue::assertPushed(ProcessRunJob::class);
    ProcessRunJob::dispatchSync($user->runs()->first()->id);

    Event::assertDispatched(RunProgress::class);
});
