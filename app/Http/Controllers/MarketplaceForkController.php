<?php

namespace App\Http\Controllers;

use App\Actions\ForkMarketplaceItem;
use App\Models\MarketplaceItem;
use App\Models\Prompt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarketplaceForkController extends Controller
{
    public function __invoke(Request $request, MarketplaceItem $item, ForkMarketplaceItem $fork): JsonResponse
    {
        abort_if($item->published_at === null, 404);

        $forked = $fork($item, $request->user());

        return response()->json([
            'id' => $forked->id,
            'type' => $forked instanceof Prompt ? 'prompt' : 'benchmark',
            'name' => $forked->name,
            'forks_count' => $item->fresh()->forks_count,
        ], 201);
    }
}
