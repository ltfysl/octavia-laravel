<?php

namespace App\Http\Middleware;

use App\Models\ApiTokenUse;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class LogApiUsage
{
    public function handle(Request $request, Closure $next): Response
    {
        $started = microtime(true);

        $response = $next($request);

        $token = $request->user()?->currentAccessToken();

        if ($token instanceof PersonalAccessToken && is_int($token->getKey()) && PersonalAccessToken::where('id', $token->getKey())->exists()) {
            $durationMs = (int) round((microtime(true) - $started) * 1000);

            ApiTokenUse::create([
                'token_id' => $token->getKey(),
                'method' => $request->getMethod(),
                'path' => substr($request->getRequestUri(), 0, 512),
                'status' => $response->getStatusCode(),
                'duration_ms' => $durationMs,
                'tokens_used' => (int) ($response->headers->get('X-Octavia-Tokens-Used') ?? 0),
                'ip' => $request->ip(),
                'created_at' => now(),
            ]);
        }

        return $response;
    }
}
