<?php

namespace Pingu\Core\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @param  string|null              $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $redirect = null, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            \Notify::warning('You must logout first');
            if ($redirect and $route = \Route::getRoutes()->getByName($redirect)) {
                return response()->redirect($route->uri());
            } else {
                return back();
            }
        }

        return $next($request);
    }
}
