<?php

namespace Pingu\Core\Theming;

use Pingu\Core\Exceptions\themeException;

class ThemeHooks
{
    /**
     * Theme Hooks class
     * @var string
     */
    protected $themeHooks;

    /**
     * Defined hooks
     * @var array
     */
    protected $hooks;

    /**
     * Set the theme hooks class
     * 
     * @param string $class
     */
    public function set(string $class)
    {
        if (!class_exists($class)) {
            throw new themeException($class.' is not a valid hook theme hook class');
        }
        $this->themeHooks = $class;
        $this->resolveHooks();
    }

    /**
     * Dispatch a theme hook
     * 
     * @param string $identifier
     * @param array  $data
     * 
     * @return bool caught
     */
    public function dispatch(string $identifier, array $data): bool
    {
        if (!$this->themeHooks) {
            return false;
        }
        if (!$this->hasHook($identifier)) {
            return false;
        }
        $this->themeHooks::$identifier(...$data);
        return true;
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
     * Resolve all hooks for the current class
     */
    protected function resolveHooks()
    {
        $ref = new \ReflectionClass($this->themeHooks);
        $methods = $ref->getMethods(\ReflectionMethod::IS_PUBLIC & \ReflectionMethod::IS_PUBLIC);
        $this->hooks = array_map(function ($method) {
            return $method->name;
        }, $methods);
    }
}