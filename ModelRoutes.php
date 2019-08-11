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

	/**
	 * Registers one slug for one model class in the laravel Route system
	 * 
	 * @param  string $class
	 */
	public function registerModel(string $class)
	{
		$slug = $class::routeSlug();
		if(isset($this->modelSlugs[$slug])){
			throw new ModelSlugAlreadyRegistered("slug for $class is already registered by ".$this->modelSlugs[$slug]);
		}
		$this->modelSlugs[$slug] = $class;
		Route::model($slug, $class);
	}

	/**
	 * Reads all classes in a folder and register their route slug
	 * 
	 * @param  string $path
	 */
	public function registerModelsFromPath(string $path)
	{
		if(!$path) return;
		$finder = new Finder;
		$iter = new ClassIterator($finder->in($path));
		foreach ($iter->getClassMap() as $classname => $fileObject) {
			$reflector = new \ReflectionClass($classname);
			if($reflector->implementsInterface(HasRouteSlugContract::class)){
				$this->registerModel($classname);
			}
		}
	}
}