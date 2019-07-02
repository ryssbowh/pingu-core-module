<?php

namespace Pingu\Core\Http\Middleware;

use Closure, Auth, DebugBar;
use Illuminate\Http\Request;

class ActivateDebugBar
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
        $user = Auth::user();
        if(env('APP_ENV') == 'local' or $user and $user->hasPermissionTo('view debug bar')){
            Debugbar::enable();
        }
        else{
            Debugbar::disable();
        }
        return $next($request);
    }
}
