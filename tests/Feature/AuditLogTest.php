<?php

use App\Models\AuditLog;
use App\Models\Prompt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('records audit entries for prompt lifecycle', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post('/prompts', [
        'name' => 'Audit me',
        'description' => 'Trail test',
        'visibility' => 'private',
        'content' => 'Say hello.',
    ])->assertRedirect();

    $prompt = Prompt::where('name', 'Audit me')->firstOrFail();

    expect(AuditLog::where('action', 'prompt.created')->where('entity_id', (string) $prompt->id)->exists())->toBeTrue()
        ->and(AuditLog::latest('id')->first()->user_id)->toBe($user->id);

    $this->delete("/prompts/{$prompt->id}")->assertRedirect();

    expect(AuditLog::where('action', 'prompt.deleted')->where('severity', 'warning')->count())->toBe(1);
});

it('scopes the audit log to the acting user', function () {
    $mine = User::factory()->create();
    $theirs = User::factory()->create();

    $this->actingAs($mine);
    AuditLog::record('run.started', 'runs', 'Mine', 'run', '1', 'My run');

    $this->actingAs($theirs);
    AuditLog::record('run.started', 'runs', 'Theirs', 'run', '2', 'Their run');

    $response = $this->actingAs($mine)->get('/audit');

    $response->assertOk()->assertInertia(
        fn ($page) => $page
            ->component('audit/Index')
            ->where('logs.data.0.description', 'Mine')
            ->missing('logs.data.1')
    );
});
