<?php

namespace Pingu\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HomepageMiddleware
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
        $home = config('core.homepage');
        if($request->path() == '/' and $home != '/') {
            return redirect($home);
        }
        return $next($request);
    }
}
