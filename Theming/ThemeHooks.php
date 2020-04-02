<?php

namespace Pingu\Core\Theming;

use Pingu\Core\Exceptions\themeException;

class ThemeHooks
{
    /**
     * Current hooks class
     * @var string
     */
    protected $class;

    /**
     * Hook list, for each defined Hook class
     * @var array
     */
    protected $hooks = [];

    /**
     * Set the theme hooks class
     * 
     * @param string $class
     */
    public function register(string $class)
    {
        if (!class_exists($class)) {
            throw new themeException($class.' is not a valid hook theme hook class');
        }
        $this->class = $class;
        $this->resolveHooks();
    }

    /**
     * Dispatch theme hooks for a renderer
     * 
     * @param string $type
     * @param string $hook
     * @param array  $data
     * 
     * @return bool caught
     */
    public function dispatch(string $type, string $hook, array $data): bool
    {
        $caught = $caught2 = $this->dispatchOne($type, $data);
        if ($hook) {
            $caught2 = $this->dispatchOne($hook, $data);
        }
        return ($caught or $caught2);
    }

    /**
     * Dispatch one hook
     * 
     * @param string $hook
     * @param array  $data
     * 
     * @return bool caught
     */
    protected function dispatchOne(string $hook, array $data): bool
    {
        if ($this->hasHook($hook)) {
            $this->class::$hook(...$data);
            return true;
        }
        return false;
    }

    /**
     * Is a hook defined
     * 
     * @param string  $name
     * 
     * @return boolean
     */
    public function hasHook(string $name)
    {
        return in_array($name, $this->hooks);
    }

    /**
     * Resolve all hooks for the current class, either from cache or built
     *
     * @return array
     */
    protected function resolveHooks()
    {
        $_this = $this;
        if (config('theme.cache')) {
            $key = config('theme.hooksCacheKey').'.'.\Theme::current()->name;
            \Log::debug($key);
            $this->hooks = \Cache::rememberForever(
                $key, function () use ($_this, $class) {
                    return $_this->buildHooks();
                }
            );
        } else {
            $this->hooks = $this->buildHooks();
        }
    }

    /**
     * Build hooks list using reflection.
     * Only list static and public methods
     * 
     * @return array
     */
    protected function buildHooks(): array
    {
        $ref = new \ReflectionClass($this->class);
        $methods = $ref->getMethods(\ReflectionMethod::IS_PUBLIC & \ReflectionMethod::IS_PUBLIC);
        return array_map(
            function ($method) {
                return $method->name;
            }, $methods
        );
    }
}