<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => fn () => $request->user()
                    ? $request->user()->only(['id', 'name', 'email', 'locale', 'onboarded_at', 'is_admin', 'email_verified_at'])
                    : null,
            ],
            'notifications' => fn () => $request->user() ? [
                'unread' => $request->user()->unreadNotifications()->count(),
                'items' => $request->user()->notifications()
                    ->limit(6)
                    ->get()
                    ->map(fn ($n) => [
                        'id' => $n->id,
                        'read' => $n->read_at !== null,
                        'run_id' => data_get($n, 'data.run_id'),
                        'run_name' => data_get($n, 'data.run_name') ?? data_get($n, 'data.item_title'),
                        'status' => data_get($n, 'data.status'),
                        'score' => data_get($n, 'data.best_score'),
                        'at' => $n->created_at?->toIso8601String(),
                    ]),
            ] : ['unread' => 0, 'items' => []],
            'runQuota' => fn () => $request->user() ? [
                'used' => RateLimiter::attempts('run:'.$request->user()->id),
                'limit' => (int) config('llm.run_quota_per_day', 50),
            ] : null,

            'flash' => fn () => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
            'appName' => config('app.name'),
        ];
    }
}
