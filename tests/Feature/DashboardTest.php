<?php

use App\Enums\RunMode;
use App\Enums\RunStatus;
use App\Models\Prompt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders dashboard with analytics', function (): void {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->create();
    $user->runs()->create([
        'prompt_id' => $prompt->id,
        'name' => 'Good run',
        'mode' => RunMode::Evaluate,
        'status' => RunStatus::Completed,
        'best_score' => 0.95,
    ]);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertOk();
    expect($response->inertiaProps('stats'))->toHaveKey('bestScore')
        ->and($response->inertiaProps('topPrompts'))->toHaveCount(1)
        ->and($response->inertiaProps('scoreHistory'))->toHaveCount(1);
});

it('hides dashboard from guests', function (): void {
    $this->get('/dashboard')->assertRedirect('/login');
});
