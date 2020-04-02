<?php

namespace Pingu\Core\Http\Middleware;

use Closure;
use Illuminate\Session\Middleware\StartSession as StartSessionBase;

class StartSession extends StartSessionBase
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   
        $segments = $request->segments();
        if ($segments and $segments[0] == config('core.apiPrefix', 'api')) {
            return $next($request);
        }
        return parent::handle($request, $next);
    }
}
