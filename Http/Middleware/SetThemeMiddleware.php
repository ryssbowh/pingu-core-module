<?php

namespace Pingu\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetThemeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {   
        $segments = $request->segments();
        if ($segments and $segments[0] == config('core.apiPrefix', 'api')) {
            return $next($request);
        }
        \Theme::setByRequest($request);
        return $next($request);
    }
}
