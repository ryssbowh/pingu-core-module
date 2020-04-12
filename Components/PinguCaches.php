<?php

namespace Pingu\Core\Components;

use Illuminate\Support\Arr;
use Pingu\Core\Exceptions\CachesException;

class PinguCaches
{
    /**
     * @var array
     */
    protected $caches = [];

    /**
     * Registers cache keys
     * 
     * @param string        $machineName
     * @param string        $name
     * @param array|string  $keys
     *
     * @throws CachesException
     */
    public function register(string $machineName, string $name, $keys)
    {
        if ($this->isRegistered($machineName)) {
            throw CachesException::defined($machineName);
        }
        $this->caches[$machineName] = [
            'name' => $name,
            'keys' => Arr::wrap($keys)
        ];
    }

    /**
     * Is a cache name registered
     * 
     * @param string  $name
     * 
     * @return boolean
     */
    public function isRegistered(string $name)
    {
        return isset($this->caches[$name]);
    }

    /**
     * Add keys to a cache
     * 
     * @param string       $name
     * @param string|array $keys
     */
    public function addKeys(string $name, $keys)
    {
        $cache = $this->get($name);
        $keys = array_merge($cache['keys'], Arr::wrap($keys));
        $this->caches[$name] = array_unique($keys);
    }

    /**
     * Get a cache details
     * 
     * @param string $name
     *
     * @throws CachesException
     * 
     * @return array
     */
    public function get(string $name): array
    {
        if ($this->isRegistered($name)) {
            return $this->caches[$name];
        }
        throw CachesException::notDefined($name);
    }

    /**
     * Get all cache details
     * 
     * @return array
     */
    public function all(): array
    {
        return $this->caches;
    }

    /**
     * Empty a cache by name
     * 
     * @param string $name
     */
    public function empty(string $name)
    {
        foreach ($this->get($name)['keys'] as $key) {
            \ArrayCache::forget($key);
        }
    }
}