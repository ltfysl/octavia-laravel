<?php

namespace App\Http\Controllers;

use App\Actions\StarMarketplaceItem;
use App\Models\MarketplaceItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarketplaceStarController extends Controller
{
    public function __invoke(Request $request, MarketplaceItem $item, StarMarketplaceItem $star): JsonResponse
    {
        abort_if($item->published_at === null, 404);

        $result = $star($item, $request->user());

        return response()->json($result);
    }
}
