<?php

namespace App\Policies;

use App\Models\BenchmarkCollection;
use App\Models\User;

class BenchmarkCollectionPolicy
{
    public function view(User $user, BenchmarkCollection $collection): bool
    {
        return $collection->user_id === $user->id;
    }

    public function update(User $user, BenchmarkCollection $collection): bool
    {
        return $collection->user_id === $user->id;
    }

    public function delete(User $user, BenchmarkCollection $collection): bool
    {
        return $collection->user_id === $user->id;
    }
}
