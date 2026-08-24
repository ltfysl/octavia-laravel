<?php

namespace App\Http\Controllers;

use App\Services\ActivityFeedService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ActivityController extends Controller
{
    public function __construct(private readonly ActivityFeedService $feed) {}

    public function index(Request $request): Response
    {
        $items = $this->feed->forUser($request->user(), (int) $request->input('limit', 20));

        return Inertia::render('activity/Index', [
            'items' => $items,
        ]);
    }

    public function api(Request $request): JsonResponse
    {
        $items = $this->feed->forUser(Auth::guard('sanctum')->user(), (int) $request->input('limit', 20));

        return response()->json(['items' => $items]);
    }
}
