<?php

namespace App\Actions;

use App\Models\MarketplaceItem;
use App\Models\User;

class StarMarketplaceItem
{
    public function __invoke(MarketplaceItem $item, User $user): array
    {
        if ($item->starredBy()->where('user_id', $user->id)->exists()) {
            $item->starredBy()->detach($user->id);
            $item->decrement('stars_count');

            return ['starred' => false, 'stars_count' => $item->fresh()->stars_count];
        }

        $item->starredBy()->attach($user->id);
        $item->increment('stars_count');

        return ['starred' => true, 'stars_count' => $item->fresh()->stars_count];
    }
}
