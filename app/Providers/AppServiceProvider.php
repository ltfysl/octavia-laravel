<?php

namespace App\Providers;

use App\Models\Run;
use App\Observers\RunObserver;
use App\Services\Llm\Contracts\LlmProvider;
use App\Services\Llm\LlmManager;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LlmManager::class);
    }

    public function boot(): void
    {
        // Settle reserved credits when a run reaches a terminal state.
        Run::observe(RunObserver::class);
        // Brute-force protection on credential endpoints.
        RateLimiter::for('auth', fn (Request $request) => Limit::perMinute(5)->by(
            mb_strtolower((string) $request->input('email')).'|'.$request->ip(),
        ));

        // Per-user daily run quota (usage-based cost control).
        RateLimiter::for('runs', fn (Request $request) => Limit::perDay(
            (int) config('llm.run_quota_per_day', 50),
        )->by('run:'.$request->user()?->id));

        // Resolve the configured default provider whenever the interface
        // is requested (e.g., RunPlayground).
        $this->app->bind(
            LlmProvider::class,
            fn (Application $app) => $app->make(LlmManager::class)->provider(),
        );
    }
}
