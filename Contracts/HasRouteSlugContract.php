<?php

namespace Pingu\Core\Contracts;

interface HasRouteSlugContract
{
    /**
     * Route slug
     * 
     * @return string
     */
    public static function routeSlug(): string;

    /**
     * Route slugs
     * 
     * @return string
     */
    public static function routeSlugs(): string;

}