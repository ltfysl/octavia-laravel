<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\Contracts\HasAbilities;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fine-grained Sanctum scope check.
 *
 * Accepts a token when it carries the exact scope (e.g. `prompts:read`),
 * the Sanctum wildcard (`*`), or a legacy coarse ability that implies it:
 *  - `read`  implies every `*:read` scope
 *  - `write` implies every scope (write includes read)
 *
 * Scope resolution goes through tokenCan() so mocked/transient tokens
 * behave exactly like persisted ones.
 *
 * Usage: ->middleware('scope:prompts:read')
 */
class EnsureTokenHasScope
{
    public function handle(Request $request, Closure $next, string $scope): Response
    {
        $token = $request->user()?->currentAccessToken();

        // Session-authenticated requests carry no access token at all.
        if ($token === null) {
            return $next($request);
        }

        if (! $this->hasScope($token, $scope)) {
            abort(403, 'Token missing required scope: '.$scope.'.');
        }

        return $next($request);
    }

    private function hasScope(HasAbilities $token, string $scope): bool
    {
        // write implies every scope; exact match and wildcard via tokenCan.
        if ($token->can($scope) || $token->can('write')) {
            return true;
        }

        if (str_ends_with($scope, ':read')) {
            // `read` implies every resource's read scope…
            if ($token->can('read')) {
                return true;
            }

            // …and a write scope on the same resource implies its read scope.
            $resource = substr($scope, 0, -5);

            return $token->can($resource.':write');
        }

        return false;
    }
}
