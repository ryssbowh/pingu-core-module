<?php

namespace Pingu\Core\Http\Controllers;

use Illuminate\Http\Request;
use Pingu\Core\Http\Controllers\BaseController;
use Pingu\Core\Traits\RendersAdminViews;

class CacheController extends BaseController
{
    use RendersAdminViews;

    /**
     * Index action
     * 
     * @return view
     */
    public function index()
    {   
        $routeCache = app()->getCachedRoutesPath();
        return $this->renderAdminView(
            ['pages.settings.cache.index'],
            'index-caches',
            [
                'caches' => \PinguCaches::all(),
                'routesAreCached' => app()->routesAreCached(),
                'cachedSince' => file_exists($routeCache) ? filemtime($routeCache) : false
            ]
        );
    }

    /**
     * Empty some cache
     * 
     * @param  Request $request
     */
    public function empty(Request $request)
    {
        $caches = $request->post('caches', []);
        if ($caches) {
            foreach ($caches as $cache) {
                \PinguCaches::empty($cache);
            }
            \Notify::success('Caches cleared');
        }
        return back();
    }

    /**
     * Caches or clears route cache
     * 
     * @param  Request $request
     */
    public function routeCache(Request $request)
    {
        if ($request->post('cacheRoutes', false)) {
            \Notify::success('Routes are now cached');
            \Artisan::call('route:cache');
        } else {
            \Notify::success('Route cache cleared');
            \Artisan::call('route:clear');
        }
        return back();
    }

    /**
     * Rebuild the route cache
     * 
     * @param Request $request
     */
    public function rebuildRouteCache(Request $request)
    {
        \Notify::success('Route cache rebuilt');
        \Artisan::call('route:cache');
        return back();
    }
}