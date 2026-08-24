<?php

use App\Enums\RunMode;
use App\Enums\RunStatus;
use App\Jobs\ProcessRunJob;
use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\User;
use App\Models\Webhook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

it('lists webhooks for the authenticated user', function (): void {
    $user = User::factory()->create();
    Webhook::factory()->for($user)->count(3)->create();

    $this->actingAs($user)->get('/settings/webhooks')
        ->assertOk();
});

it('creates a webhook', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/settings/webhooks', [
        'url' => 'https://example.test/webhook',
        'events' => ['run.completed'],
        'description' => 'My webhook',
    ])->assertRedirect();

    $this->assertDatabaseHas('webhooks', [
        'user_id' => $user->id,
        'url' => 'https://example.test/webhook',
    ]);
});

it('deletes a webhook', function (): void {
    $user = User::factory()->create();
    $webhook = Webhook::factory()->for($user)->create();

    $this->actingAs($user)->delete("/settings/webhooks/{$webhook->id}")
        ->assertRedirect();

    $this->assertModelMissing($webhook);
});

it('dispatches webhook delivery when a run completes', function (): void {
    Http::fake(['https://example.test/webhook' => Http::response('OK', 200)]);

    $user = User::factory()->create();
    $webhook = Webhook::factory()->for($user)->create([
        'url' => 'https://example.test/webhook',
        'events' => ['run.completed'],
    ]);
    $prompt = Prompt::factory()->for($user)->create();
    $prompt->versions()->create(['version' => 1, 'content' => 'Test', 'changelog' => null]);
    $benchmark = Benchmark::factory()->for($user)->create();
    $run = $user->runs()->create([
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'name' => 'Test run',
        'mode' => RunMode::Evaluate,
        'status' => RunStatus::Pending,
        'provider' => 'mock',
    ]);

    ProcessRunJob::dispatch($run->id);
    $this->artisan('queue:work', ['--max-jobs' => 2, '--stop-when-empty' => true]);

    $run->refresh();
    $this->assertDatabaseHas('webhook_deliveries', [
        'webhook_id' => $webhook->id,
        'event' => 'run.completed',
    ]);
});
