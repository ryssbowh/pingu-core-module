<?php 

namespace Pingu\Core;

use Pingu\Core\Contracts\Models\HasRouteSlugContract;
use Pingu\Core\Exceptions\ModelSlugAlreadyRegistered;
use Symfony\Component\Finder\Finder;
use hanneskod\classtools\Iterator\ClassIterator;

class ModelRoutes
{
	protected $modelSlugs = [];

	/**
	 * Registers one slug for one model class in the laravel Route system
	 * 
	 * @param  string $class
	 */
	public function registerSlugFromObject(HasRouteSlugContract $object)
	{
		$slug = $object::routeSlug();
		$slugs = $object::routeSlugs();
		$class = get_class($object);
		if(isset($this->modelSlugs[$slug])){
			throw new ModelSlugAlreadyRegistered("slug '$slug' for $class is already registered by ".$this->modelSlugs[$slug]);
		}
		if(isset($this->modelSlugs[$slugs])){
			throw new ModelSlugAlreadyRegistered("slug '$slugs' for $class is already registered by ".$this->modelSlugs[$slugs]);
		}
		$this->modelSlugs[$slug] = $object;
		$this->modelSlugs[$slugs] = $object;
		\Route::model($slug, $class);
	}

	public function getModel(string $slug)
	{
		return $this->modelSlugs[$slug] ?? null;
	}
}