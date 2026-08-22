<?php

use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('throttles run creation beyond the daily quota', function () {
    config()->set('llm.run_quota_per_day', 3);

    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->withContent('Test.')->create();
    $benchmark = Benchmark::factory()->for($user)->withContainsCase('x')->create();

    foreach (range(1, 3) as $i) {
        $this->actingAs($user)->post('/runs', [
            'prompt_id' => $prompt->id,
            'benchmark_id' => $benchmark->id,
            'mode' => 'evaluate',
        ])->assertRedirect();
    }

    $this->actingAs($user)->post('/runs', [
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'mode' => 'evaluate',
    ])->assertStatus(429);
});

it('counts the quota per user, not globally', function () {
    config()->set('llm.run_quota_per_day', 1);

    $user = User::factory()->create();
    $other = User::factory()->create();

    $prompts = Prompt::factory()->count(2)->for($user)->withContent('Test.')->create();
    $benchmarkForUser = Benchmark::factory()->for($user)->withContainsCase('x')->create();
    $benchmarkForOther = Benchmark::factory()->for($other)->withContainsCase('y')->create();

    $this->actingAs($user)->post('/runs', [
        'prompt_id' => $prompts[0]->id,
        'benchmark_id' => $benchmarkForUser->id,
        'mode' => 'evaluate',
    ])->assertRedirect();

    // Different user is unaffected by the first user's quota.
    Queue::fake();

    $this->actingAs($other)->post('/runs', [
        'prompt_id' => $other->prompts()->first()?->id ?? Prompt::factory()->for($other)->withContent('Test.')->create()->id,
        'benchmark_id' => $benchmarkForOther->id,
        'mode' => 'evaluate',
    ])->assertStatus(302);
});
