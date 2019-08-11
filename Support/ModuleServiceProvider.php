<?php

namespace Pingu\Core\Support;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{

	/*
     * Where are the models located
     */
    protected $modelFolder = 'Entities';
    
	/**
	 * Will merge (and replace) recursively config arrays. To be used to replace a vendor
	 * config without having to publish it
	 * 
	 * @param  string $path
	 * @param  string $key
	 */
	public function replaceConfigFrom($path, $key)
	{
		$config = $this->app['config']->get($key, []);

        $this->app['config']->set($key, array_replace_recursive($config, require $path));
	}

	/**
     * Registers all the slugs for this module's models
     */
    public function registerModelSlugs(string $path)
    {
        \ModelRoutes::registerModelsFromPath(realpath($path));
    }

}