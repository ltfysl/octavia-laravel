<?php

namespace App\Broadcasting;

use App\Models\Run;
use App\Models\User;

class AuthorizeRunChannel
{
    /**
     * Owner-only access to a run's private progress channel.
     * Team sharing can be layered here later via Run::visibleTo.
     */
    public function __invoke(User $user, int $runId): bool
    {
        $run = Run::find($runId);

        return $run !== null && $run->user_id === $user->id;
    }
}
