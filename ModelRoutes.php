<?php 

namespace Pingu\Core;

use Illuminate\Support\Facades\Route;
use Pingu\Core\Contracts\Models\HasRouteSlugContract;
use Pingu\Core\Exceptions\ModelSlugAlreadyRegistered;
use Symfony\Component\Finder\Finder;
use hanneskod\classtools\Iterator\ClassIterator;

class ModelRoutes
{
	protected $modelSlugs = [];

	public function registerSlug(string $name, string $class)
	{
		if(isset($this->modelSlugs[$name])){
			throw new ModelSlugAlreadyRegistered("slug for $class is already registered by ".$this->modelSlugs[$name]);
		}
		$this->modelSlugs[$name] = $class;
		Route::model($class::routeSlug(), $class);
	}

	/**
	 * Reads all classes in a folder and register their route slug
	 * @param  string $path
	 */
	public function registerSlugsFromPath(string $path)
	{
		if(!$path) return;
		$finder = new Finder;
		$iter = new ClassIterator($finder->in($path));
		foreach ($iter->getClassMap() as $classname => $fileObject) {
			$reflector = new \ReflectionClass($classname);
			if($reflector->implementsInterface(HasRouteSlugContract::class)){
				$this->registerSlug($classname::routeSlug(), $classname);
			}
		}
	}
}