<?php

namespace Pingu\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Published
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $slug)
    {
        $entity = $request->route($slug);
        if (!$entity->published) {
            throw new HttpException(404);
        }
        return $next($request);
    }
}
