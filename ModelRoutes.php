<?php 

namespace Pingu\Core;

use Pingu\Core\Contracts\HasRouteSlugContract;
use Pingu\Core\Exceptions\ModelSlugAlreadyRegistered;
use Symfony\Component\Finder\Finder;
use hanneskod\classtools\Iterator\ClassIterator;

class ModelRoutes
{
    protected $modelSlugs = [];

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

    public function registerSlug(string $slug, string $class)
    {
        if (isset($this->modelSlugs[$slug])) {
            throw new ModelSlugAlreadyRegistered("slug '$slug' for $class is already registered by ".$this->modelSlugs[$slug]);
        }
        $this->modelSlugs[$slug] = $class;
    }

    public function getModel(string $slug): string
    {
        return $this->modelSlugs[$slug] ?? null;
    }
}