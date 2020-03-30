<?php 
namespace Pingu\Core\Facades;

use Illuminate\Support\Facades\Facade;

class ThemeHooks extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'core.themeHooks';
    }
}
