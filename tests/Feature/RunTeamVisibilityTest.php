<?php

use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\Run;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeRun(User $user, string $name): Run
{
    $prompt = Prompt::factory()->for($user)->withContent('Test.')->create();
    $benchmark = Benchmark::factory()->for($user)->withContainsCase('x')->create();

    return $user->runs()->create([
        'prompt_id' => $prompt->id,
        'benchmark_id' => $benchmark->id,
        'name' => $name,
        'mode' => 'evaluate',
        'status' => 'completed',
        'provider' => 'mock',
    ]);
}

it('allows team mates to view a shared run', function () {
    $owner = User::factory()->create();
    $mate = User::factory()->create();
    $team = Team::create(['name' => 'Team', 'owner_id' => $owner->id]);
    TeamMember::create(['team_id' => $team->id, 'user_id' => $mate->id, 'role' => 'member']);

    $run = makeRun($owner, 'Shared run');

    $this->actingAs($mate)->get("/runs/{$run->id}")->assertOk();
});

it('blocks strangers from viewing a run', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $run = makeRun($owner, 'Private run');

    $this->actingAs($stranger)->get("/runs/{$run->id}")->assertForbidden();
});

it('shows team runs in the runs index with owner attribution', function () {
    $owner = User::factory()->create();
    $mate = User::factory()->create();
    $team = Team::create(['name' => 'Team', 'owner_id' => $owner->id]);
    TeamMember::create(['team_id' => $team->id, 'user_id' => $mate->id, 'role' => 'member']);

    $run = makeRun($owner, 'Team run');

    $this->actingAs($mate)->get('/runs')
        ->assertInertia(fn ($page) => $page
            ->has('runs.data', 1)
            ->where('runs.data.0.id', $run->id)
            ->where('runs.data.0.owner.name', $owner->name));
});

it('excludes stranger runs from the index', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    makeRun($owner, 'Not visible');

    $this->actingAs($stranger)->get('/runs')
        ->assertInertia(fn ($page) => $page->has('runs.data', 0));
});
