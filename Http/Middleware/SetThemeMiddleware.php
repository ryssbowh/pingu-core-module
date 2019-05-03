<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Asset;
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
        $segments = $request->segments();
        
        if( isset( $segments[0]) and $segments[0] == 'admin' ){
            if( !\Theme::exists(config('core.adminTheme'))) {
                throw new \Exception(config('core.adminTheme' )." isn't a valid admin theme", 1);
            }
            \Theme::set( config('core.adminTheme' ));
        }
        else{
            if( !\Theme::exists(config('core.frontTheme'))) {
                throw new \Exception(config('core.frontTheme' )." isn't a valid front theme", 1);
            }
            \Theme::set( config('core.frontTheme' ));
        }
        $theme = \Theme::current();
        $assetPath = config('core.themes.themes_path').'/'.$theme->name.'/'.$theme->assetPath;
        Asset::container('theme')->add('css', $assetPath.'/'.$theme->name.'.css');
        Asset::container('theme')->add('js', $assetPath.'/'.$theme->name.'.js');
        return $next($request);
    }
}
