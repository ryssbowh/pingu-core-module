<?php

namespace Pingu\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Pingu\Core\Exceptions\MaintenanceModeException;
use Illuminate\Contracts\Foundation\Application;

class CheckForMaintenanceMode
{
    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();

        if ($this->app->isDownForMaintenance() and !($route->named('user.loginForm') or $route->named('user.login'))) {
            
            $user = $this->app->auth->user();
            if($user and $user->can('view site in maintenance mode')) {
                return $next($request);
            }
            $data = json_decode(file_get_contents($this->app->storagePath().'/framework/down'), true);

            \Theme::set(config('core.frontTheme'));
            throw new MaintenanceModeException($data['time'], config('core.maintenance.retryAfter'), config('core.maintenance.message'));
        }

        return $next($request);
    }
}