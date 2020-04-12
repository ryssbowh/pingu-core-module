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
     * @param Request $request   
     * @param string  $repository
     * 
     * @return view
     */
    public function index()
    {   
        return $this->renderAdminView(
            ['pages.settings.cache.index'],
            'index-caches',
            [
                'caches' => \PinguCaches::all()
            ]
        );
    }

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
}