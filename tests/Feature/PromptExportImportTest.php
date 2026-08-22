<?php

use App\Models\Prompt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;

uses(RefreshDatabase::class);

it('exports a prompt as JSON for the owner', function () {
    $user = User::factory()->create();
    $prompt = Prompt::factory()->for($user)->withContent('Exported content.')->create();

    $response = $this->actingAs($user)->get("/prompts/{$prompt->id}/export");

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/json')
        ->assertJsonPath('name', $prompt->name)
        ->assertJsonCount(1, 'versions');
});

it('rejects export for foreign prompts', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $prompt = Prompt::factory()->for($owner)->withContent('Private.')->create();

    $this->actingAs($other)->get("/prompts/{$prompt->id}/export")->assertForbidden();
});

it('imports a prompt from a valid JSON export', function () {
    $user = User::factory()->create();

    $json = json_encode([
        'name' => 'Imported Prompt',
        'description' => 'From JSON',
        'versions' => [
            ['version' => 1, 'content' => 'First version.', 'changelog' => 'Initial'],
            ['version' => 2, 'content' => 'Second version.', 'changelog' => 'Improved'],
        ],
    ]);

    $this->actingAs($user)->post('/prompts/import', [
        'file' => File::fake()->createWithContent('import.json', $json),
    ])->assertRedirect(route('prompts.show', Prompt::first()));

    $prompt = Prompt::first();
    expect($prompt->name)->toBe('Imported Prompt')
        ->and($prompt->versions()->count())->toBe(2)
        ->and($prompt->currentVersion->content)->toBe('Second version.');
});

it('rejects invalid prompt import JSON', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/prompts/import', [
        'file' => File::fake()->createWithContent('bad.json', 'not json'),
    ])->assertRedirect();
});
