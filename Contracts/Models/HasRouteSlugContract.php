<?php

namespace Pingu\Core\Contracts\Models;

interface HasRouteSlugContract
{
    /**
     * Route slug
     * 
     * @return string
     */
    public static function routeSlug();

    /**
     * Route slug (plural)
     * 
     * @return string
     */
    public static function routeSlugs();

}