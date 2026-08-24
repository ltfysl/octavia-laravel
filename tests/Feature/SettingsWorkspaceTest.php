<?php

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('settings workspace page shows user teams', function () {
    $user = User::factory()->create();
    $team = Team::factory()->recycle($user)->create(['owner_id' => $user->id]);

    $this->actingAs($user)
        ->get('/settings/workspace')
        ->assertInertia(fn ($page) => $page
            ->component('settings/Workspace')
            ->has('teams', 1)
            ->where('teams.0.name', $team->name)
        );
});
