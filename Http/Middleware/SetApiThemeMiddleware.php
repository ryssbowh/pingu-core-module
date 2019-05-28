<?php

namespace Pingu\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetApiThemeMiddleware
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
        $theme = $request->all()['_theme'] ?? false;
        
        if($theme and \Theme::exists($theme)){
            \Theme::set($theme);
        }

        return $next($request);
    }
}
