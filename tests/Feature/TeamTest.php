<?php

use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a team owned by the authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/teams', ['name' => 'My Team'])
        ->assertRedirect();

    $team = Team::first();
    expect($team->owner_id)->toBe($user->id)
        ->and($team->hasMember($user))->toBeTrue()
        ->and($team->roleOf($user))->toBe('owner');
});

it('prevents duplicate membership when inviting', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $team = Team::create(['name' => 'T', 'owner_id' => $owner->id]);
    $team->members()->create(['user_id' => $member->id, 'role' => 'member']);

    $this->actingAs($owner)->post("/teams/{$team->id}/invite", [
        'email' => $member->email,
        'role' => 'admin',
    ])->assertSessionHas('error');
});

it('validates invite email exists in users table', function () {
    $owner = User::factory()->create();
    $team = Team::create(['name' => 'T', 'owner_id' => $owner->id]);

    $this->actingAs($owner)->post("/teams/{$team->id}/invite", [
        'email' => 'ghost@example.com',
        'role' => 'member',
    ])->assertSessionHasErrors('email');
});

it('allows only the owner to delete a team', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $team = Team::create(['name' => 'T', 'owner_id' => $owner->id]);

    $this->actingAs($other)->delete("/teams/{$team->id}")->assertForbidden();
    $this->actingAs($owner)->delete("/teams/{$team->id}")->assertRedirect();

    expect(Team::find($team->id))->toBeNull();
});

it('makes prompts visible to team mates via scopeVisibleTo', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $outsider = User::factory()->create();

    $team = Team::create(['name' => 'T', 'owner_id' => $owner->id]);
    $team->members()->create(['user_id' => $member->id, 'role' => 'member']);

    $shared = Prompt::factory()->for($owner)->withContent('Team prompt.')->create();
    Prompt::factory()->for($outsider)->withContent('Private.')->create();

    $visible = Prompt::visibleTo($member)->get();
    expect($visible->pluck('id'))->toContain($shared->id)
        ->and($visible->count())->toBe(1);

    $outsiderVisible = Prompt::visibleTo($outsider)->get();
    expect($outsiderVisible->pluck('id'))->not->toContain($shared->id);
});

it('makes benchmarks visible to team mates via scopeVisibleTo', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();

    $team = Team::create(['name' => 'T', 'owner_id' => $owner->id]);
    $team->members()->create(['user_id' => $member->id, 'role' => 'member']);

    $shared = Benchmark::factory()->for($owner)->withContainsCase('x')->create();

    $visible = Benchmark::visibleTo($member)->get();
    expect($visible->pluck('id'))->toContain($shared->id);
});
