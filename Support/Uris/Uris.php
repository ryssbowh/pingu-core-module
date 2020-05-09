<?php

namespace Pingu\Core\Support\Uris;

use Illuminate\Support\Arr;
use Pingu\Core\Exceptions\UriException;
use Pingu\Entity\Traits\AccessUris;

abstract class Uris
{   
    /**
     * @var array
     */
    protected $uris = [];

    public function __construct()
    {
        $this->replaceMany($this->uris());
    }

    /**
     * Extra uris
     * 
     * @return array
     */
    abstract protected function uris(): array;

    /**
     * Returns all uris
     * 
     * @return array
     */
    public function all(): array
    {
        return $this->uris;
    }

    /**
     * Add a uri
     * 
     * @param string $action
     * @param string $uri
     *
     * @throws UriException
     */
    public function add(string $action, string $uri)
    {
        if ($this->has($action)) {
            throw UriException::defined($action, $this);
        }
        $uri = $this->replaceSlugs($uri);
        $this->uris[$action] = $uri;
    }

    /**
     * Add many uris
     * 
     * @param array $uris
     */
    public function addMany(array $uris)
    {
        foreach ($uris as $action => $uri) {
            $this->add($action, $uri);
        }
    }

    /**
     * Replaces many uris
     * 
     * @param array  $uris
     */
    public function replaceMany(array $uris)
    {
        foreach ($uris as $action => $uri) {
            $this->replace($action, $uri);
        }
    }

    /**
     * Replace one uri
     * 
     * @param string $action
     * @param string $uri
     */
    public function replace(string $action, string $uri)
    {
        $this->uris[$action] = $this->replaceSlugs($uri);
    }

    /**
     * Does an uri exist for an action
     * 
     * @param string  $action
     * 
     * @return boolean
     */
    public function has(string $action): bool
    {
        return isset($this->uris[$action]);
    }

    /**
     * Remove the uri for an action
     * 
     * @param string $action
     */
    public function remove(string $action): bool
    {
        if ($this->has($action)) {
            unset($this->uris[$action]);
        }
    }

    /**
     * Slugs that can be replaced
     * 
     * @return array
     */
    protected function replacableSlugs(): array
    {
        return [];
    }

    /**
     * Replaces slugs in the uri
     * 
     * @param  string $uri
     * @return string
     */
    protected function replaceSlugs(string $uri)
    {
        return strtr($uri, $this->replacableSlugs());
    }

    /**
     * Forward all calls to get
     * 
     * @param string $name
     * @param mixed $args
     * 
     * @return mixed
     */
    public function __call($name, $args)
    {
        return $this->get($name);
    }

    /**
     * Getter
     * 
     * @param string  $action
     * @param ?string $prefix
     *
     * @throws UriException
     * 
     * @return string
     */
    public function get(string $action, ?string $prefix = null)
    {   
        $prefix = $prefix ? trim($prefix, '/').'/' : '';
        if (isset($this->uris[$action])) {
            return '/'.$prefix.trim($this->uris[$action], '/');
        }
        throw UriException::undefined($action, $this);
    }

    /**
     * Transform an uri, replacing all slugs by values in replacements array,
     * if an object is found in replacements, its getRouteKey method will be used for the replacement
     * 
     * @param string  $action
     * @param mixed   $replacements
     * @param ?string $prefix
     * @param array $parameters
     * 
     * @return string
     */
    public function make(string $action, $replacements = [], ?string $prefix = null, array $parameters = [])
    {
        $replacements = Arr::wrap($replacements);
        $uri = replaceUriSlugs($this->get($action, $prefix), $replacements);
        if ($parameters) {
            array_walk($parameters, function (&$value, $key) {
                $value = $key.'='.$value;
            });
            $uri .= '?'.implode('&', $parameters);
        }
        return $uri;
    }
}