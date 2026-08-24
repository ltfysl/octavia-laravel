<?php

use App\Models\Prompt;
use App\Models\User;
use App\Services\Llm\Contracts\LlmProvider;
use App\Services\Llm\LlmResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('explains differences between two prompt versions', function (): void {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->create();
    $v1 = $prompt->versions()->create(['version' => 1, 'content' => 'You are a helpful assistant.', 'created_by' => $user->id]);
    $v2 = $prompt->versions()->create(['version' => 2, 'content' => 'You are a helpful assistant. Reply in JSON.', 'created_by' => $user->id]);

    app()->instance(LlmProvider::class, new class implements LlmProvider
    {
        public function complete(array $messages, array $options = []): LlmResponse
        {
            return new LlmResponse(json_encode([
                'summary' => 'The new version asks for JSON output.',
                'changes' => [
                    ['type' => 'minor', 'description' => 'Added JSON requirement.', 'impact' => 'positive'],
                ],
                'recommendation' => 'Test with nested objects.',
            ]), 10, 15);
        }
    });

    $this->actingAs($user)->postJson("/prompts/{$prompt->id}/diff-explain", [
        'from_version_id' => $v1->id,
        'to_version_id' => $v2->id,
    ])
        ->assertOk()
        ->assertJsonPath('summary', 'The new version asks for JSON output.')
        ->assertJsonPath('recommendation', 'Test with nested objects.')
        ->assertJsonStructure(['summary', 'changes', 'recommendation']);
});

it('falls back gracefully when the model returns invalid json', function (): void {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->create();
    $v1 = $prompt->versions()->create(['version' => 1, 'content' => 'A', 'created_by' => $user->id]);
    $v2 = $prompt->versions()->create(['version' => 2, 'content' => 'B', 'created_by' => $user->id]);

    app()->instance(LlmProvider::class, new class implements LlmProvider
    {
        public function complete(array $messages, array $options = []): LlmResponse
        {
            return new LlmResponse('not valid json', 0, 0);
        }
    });

    $this->actingAs($user)->postJson("/prompts/{$prompt->id}/diff-explain", [
        'from_version_id' => $v1->id,
        'to_version_id' => $v2->id,
    ])
        ->assertOk()
        ->assertJsonPath('summary', 'The model did not return a structured explanation.');
});
