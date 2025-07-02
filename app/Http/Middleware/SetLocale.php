<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Get locale from request parameter, session, or default
        $locale = $request->get('lang', Session::get('locale', config('app.locale')));

        // Validate locale
        $availableLocales = ['en', 'lt'];
        if (! in_array($locale, $availableLocales)) {
            $locale = config('app.locale');
        }

        // Set application locale
        App::setLocale($locale);

        // Store in session for future requests
        Session::put('locale', $locale);

        return $next($request);
    }
}
