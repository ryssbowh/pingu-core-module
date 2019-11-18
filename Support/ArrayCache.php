<?php 

namespace Pingu\Core\Support;

use Closure;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\Arr;

/**
 * Cache helper to be able to handle cache keys as dotted arrays.
 * Allows forgetting cache for a key and all its children
 *
 * Example : something is cached at 'mycache.value.random'
 * calling ArrayCache::forget('mycache') will forget that value
 */
class ArrayCache
{
    protected $keys;
    protected $cacheKey = 'core.arrayCache';

    public function __construct()
    {
        $this->load();
    }

    protected function load()
    {
        $this->keys = \Cache::get($this->cacheKey) ?? [];
    }

    protected function addKey(string $key)
    {
        if (!Arr::has($this->keys, $key)) {
            data_set($this->keys, $key, []);
            $this->write();
        }
    }

    protected function write()
    {
        \Cache::forever($this->cacheKey, $this->keys);
    }

    public function remember($key, $seconds, Closure $value)
    {
        $this->addKey($key);
        return \Cache::remember($key, $seconds, $value);
    }

    public function rememberForever($key, Closure $value)
    {
        $this->addKey($key);
        return \Cache::rememberForever($key, $value);
    }

    public function forever($key, $value)
    {
        $this->addKey($key);
        \Cache::forever($key, $value);
    }

    public function put($key, $value, $seconds = null)
    {
        $this->addKey($key);
        parent::put($key, $value);
    }

    public function putMany(array $values, $seconds)
    {   
        foreach (array_keys($values) as $key) {
            $this->addKey($key);
        }
        parent::putMany($values, $seconds);
    }

    public function add($key, $value, $seconds)
    {   
        $this->addKey($key);
        return \Cache::add($key, $value, $seconds);
    }

    public function forget($key)
    {
        if ($data = data_get($this->keys, $key)) {
            $this->performForget($key, data_get($this->keys, $key));
            Arr::forget($this->keys, $key);
            $this->write();
        }
    }

    protected function performForget(string $key, array $array)
    {
        if (!$array) {
            \Cache::forget($key);
            return;
        }
        foreach ($array as $newkey => $array) {
            $this->performForget($key.'.'.$newkey, $array);
        }
    }
}