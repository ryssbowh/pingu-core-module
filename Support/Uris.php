<?php

namespace Pingu\Core\Support;

use Illuminate\Support\Arr;
use Pingu\Core\Exceptions\UriException;
use Pingu\Entity\Traits\AccessUris;

abstract class Uris
{
    protected $uris = [];

    public function __construct()
    {
        $this->uris = $this->uris();
    }

    abstract protected function uris(): array;

    public function all(): array
    {
        return $this->uris;
    }

    public function add(string $action, string $uri)
    {
        if ($this->has($action)) {
            throw UriException::defined($action, $this);
        }
        $this->uris[$action] = $uri;
    }

    public function addMany(array $uris)
    {
        foreach ($uris as $action => $uri) {
            $this->add($action, $uri);
        }
    }

    public function replaceMany(array $uris)
    {
        foreach ($uris as $action => $uri) {
            $this->replace($action, $uri);
        }
    }

    public function replace(string $action, string $uri)
    {
        $this->uris[$action] = $uri;
    }

    public function has(string $action): bool
    {
        return isset($this->uris[$action]);
    }

    public function remove(string $action): bool
    {
        if ($this->has($action)) {
            unset($this->uris[$action]);
        }
    }

    public function __call($name, $args)
    {
        return $this->get($name);
    }

    /**
     * Getter
     * 
     * @param string $action
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
     * @param mixed $replacements
     * @param ?string $prefix
     * 
     * @return string
     */
    public function make(string $action, $replacements = [], ?string $prefix = null)
    {
        $replacements = Arr::wrap($replacements);
        $uri = $this->get($action, $prefix);
        return replaceUriSlugs($uri, $replacements);
    }
}