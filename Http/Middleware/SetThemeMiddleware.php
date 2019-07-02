<?php

namespace Pingu\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetThemeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {   
        \Theme::setByRequest($request);
        return $next($request);
    }
}
