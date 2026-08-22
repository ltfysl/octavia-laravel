<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->user()?->locale
            ?? $request->cookie('octavia_locale')
            ?? config('app.locale');

        if (in_array($locale, ['en', 'de'], true)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
