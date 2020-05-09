<?php

namespace Pingu\Core\Components;

use Pingu\Core\Contracts\HasRouteSlugContract;
use Pingu\Core\Exceptions\ModelSlugAlreadyRegistered;
use Symfony\Component\Finder\Finder;
use hanneskod\classtools\Iterator\ClassIterator;

class RouteSlugs
{
    protected $routeSlugs = [];

    /**
     * Registers one slug for one model class in the laravel Route system
     * 
     * @param string $class
     */
    public function registerSlugFromObject(HasRouteSlugContract $object)
    {
        $slug = $object::routeSlug();
        $slugs = $object::routeSlugs();
        $class = get_class($object);
        $this->registerSlug($slug, $class);
        $this->registerSlug($slugs, $class);
        \Route::model($slug, $class);
    }

    /**
     * Registers a single slug
     * 
     * @param string $slug 
     * @param string $class
     *
     * @throws ModelSlugAlreadyRegistered
     */
    public function registerSlug(string $slug, string $class)
    {
        if (isset($this->routeSlugs[$slug])) {
            throw new ModelSlugAlreadyRegistered("slug '$slug' for $class is already registered by ".$this->routeSlugs[$slug]);
        }
        $this->routeSlugs[$slug] = $class;
    }

    /**
     * Get the model associated to a slug
     * 
     * @param string $slug
     * 
     * @return string
     */
    public function getModel(string $slug): string
    {
        return $this->routeSlugs[$slug] ?? null;
    }
}