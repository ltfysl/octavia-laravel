<?php

namespace App\Policies;

use App\Models\ProviderKey;
use App\Models\User;

class ProviderKeyPolicy
{
    public function update(User $user, ProviderKey $key): bool
    {
        return $user->id === $key->user_id;
    }

    public function delete(User $user, ProviderKey $key): bool
    {
        return $user->id === $key->user_id;
    }
}
