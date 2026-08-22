<?php

use App\Exceptions\InsufficientCreditsException;
use App\Models\Benchmark;
use App\Models\CreditTransaction;
use App\Models\Prompt;
use App\Models\User;
use App\Services\CreditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

function makeRunPayload(User $user, array $overrides = []): array
{
    $prompt = Prompt::factory()->for($user)->withContent('Test.')->create();
    $benchmark = Benchmark::factory()->for($user)->withContainsCase('x')->create();

    return array_merge([
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'mode' => 'evaluate',
    ], $overrides);
}

it('grants signup credits on registration', function () {
    $user = User::factory()->create(['credits_balance' => 0]);

    app(CreditService::class)->grantSignup($user);

    expect($user->fresh()->credits_balance)->toBe(CreditService::SIGNUP_GRANT)
        ->and($user->creditTransactions()->where('reason', 'signup_grant')->exists())->toBeTrue();
});

it('consumes credits atomically and writes a ledger row', function () {
    $user = User::factory()->create(['credits_balance' => 10]);

    app(CreditService::class)->consume($user, 4, CreditService::REASON_RUN_RESERVED, ['run_name' => 'X']);

    expect($user->fresh()->credits_balance)->toBe(6)
        ->and(CreditTransaction::where('reason', 'run_reserved')->first()->delta)->toBe(-4);
});

it('rejects consumption beyond the balance without a ledger row', function () {
    $user = User::factory()->create(['credits_balance' => 2]);

    app(CreditService::class)->consume($user, 5, CreditService::REASON_RUN_RESERVED);
})->throws(InsufficientCreditsException::class);

it('blocks run start when the balance cannot cover the reservation', function () {
    $user = User::factory()->create(['credits_balance' => 0]);

    $this->actingAs($user)->post('/runs', makeRunPayload($user))
        ->assertSessionHasErrors('credits');

    expect($user->fresh()->runs()->count())->toBe(0);
});

it('refunds unused reserved credits when a run completes', function () {
    Queue::fake();
    config(['llm.evolution.max_steps' => 8]);
    $user = User::factory()->create(['credits_balance' => 100]);

    $this->actingAs($user)->post('/runs', makeRunPayload($user, ['mode' => 'optimize']));
    $run = $user->runs()->first();

    // Simulate the engine executing only 3 of 8 steps.
    $run->steps()->create(['number' => 1, 'phase' => 'evaluate', 'score' => 0.5, 'prompt_content' => 'x']);
    $run->steps()->create(['number' => 2, 'phase' => 'evaluate', 'score' => 0.6, 'prompt_content' => 'x']);
    $run->steps()->create(['number' => 3, 'phase' => 'evaluate', 'score' => 0.7, 'prompt_content' => 'x']);

    $reserved = 100 - $user->fresh()->credits_balance; // 8 consumed
    $run->forceFill(['status' => 'completed', 'finished_at' => now()])->save();

    expect($reserved)->toBe(8)
        ->and($user->fresh()->credits_balance)->toBe(97); // 100 - 8 reserved + (8 - 3) refunded
});
