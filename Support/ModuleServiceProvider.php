<?php

namespace Pingu\Core\Support;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    
    /**
     * Will merge (and replace) recursively config arrays. To be used to replace a vendor
     * config without having to publish it
     * 
     * @param string $path
     * @param string $key
     */
    public function replaceConfigFrom($path, $key)
    {
        $config = $this->app['config']->get($key, []);

        $this->app['config']->set($key, array_replace_recursive($config, require $path));
    }

    /**
     * Registers entities
     */
    public function registerEntities(array $entityClasses)
    {
        foreach ($entityClasses as $class) {
            (new $class)->register();
        }
    }

}