<?php

namespace Pingu\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Pingu\Permissions\Middleware\PermissionMiddleware;

class IndexSettings
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
        $repository = $request->route()->parameter('setting_section');
        $model = \Permissions::getPermissionableModel();

        $permissions = $repository->accessPermissions();
        
        return app(PermissionMiddleware::class)->handle($request, $next, $permissions);
    }
}
