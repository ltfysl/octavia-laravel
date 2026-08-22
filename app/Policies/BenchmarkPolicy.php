<?php

namespace App\Policies;

use App\Models\Benchmark;
use App\Models\User;

class BenchmarkPolicy
{
    public function view(User $user, Benchmark $benchmark): bool
    {
        return $benchmark->user_id === $user->id || $benchmark->visibility->value === 'public';
    }

    public function update(User $user, Benchmark $benchmark): bool
    {
        return $benchmark->user_id === $user->id;
    }

    public function delete(User $user, Benchmark $benchmark): bool
    {
        return $benchmark->user_id === $user->id;
    }
}
