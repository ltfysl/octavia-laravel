<?php

namespace App\Policies;

use App\Models\Run;
use App\Models\User;

class RunPolicy
{
    /**
     * Runs are visible to their owner and to team mates sharing a team
     * with the owner — mirroring the team-aware visibility of prompts
     * and benchmarks. Only the owner can update/cancel.
     */
    public function view(User $user, Run $run): bool
    {
        return $run->user_id === $user->id
            || $user->teamMateIds()->contains($run->user_id);
    }

    public function update(User $user, Run $run): bool
    {
        return $run->user_id === $user->id;
    }
}
