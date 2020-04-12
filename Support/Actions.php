<?php

namespace Pingu\Core\Support;

use Closure;
use Pingu\Core\Exceptions\ActionsException;
use Pingu\Entity\Events\ActionsRetrieved;

abstract class Actions
{
    /**
     * @var array
     */
    protected $actions = [];

    public function __construct()
    {
        $this->actions = $this->actions();
    }

    abstract protected function actions(): array;

    /**
     * Add an action
     * 
     * @param string       $name
     * @param string       $label
     * @param Closure      $url
     * @param Closure|null $accessCallback
     *
     * @throws ActionsException
     *
     * @return Actions
     */
    public function add(string $name, string $label, Closure $url, ?Closure $accessCallback = null)
    {
        if ($this->has($name)) {
            throw ActionsException::defined($name, $this);
        }
        return $this->replace($name, $label, $url, $accessCallback);
    }

    /**
     * Adds an action (no checks)
     * 
     * @param string       $name
     * @param string       $label
     * @param Closure      $url
     * @param Closure|null $accessCallback
     * 
     * @return Actions
     */
    public function replace(string $name, string $label, Closure $url, ?Closure $accessCallback = null)
    {
        $this->actions[$name] = [
            'label' => $label,
            'url' => $url,
            'access' => $accessCallback
        ];
        return $this;
    }

    /**
     * Adds many actions
     * 
     * @param array $actions
     *
     * @return Actions
     */
    public function addMany(array $actions)
    {
        foreach ($actions as $name => $action) {
            $this->add($name, $action['label'], $action['url'], $action['access'] ?? null);
        }
        return $this;
    }

    /**
     * Replaces many actions
     * 
     * @param array  $actions
     * 
     * @return Actions
     */
    public function replaceMany(array $actions)
    {
        foreach ($actions as $name => $action) {
            $this->replace($name, $action['label'], $action['url'], $action['access'] ?? null);
        }
        return $this;
    }

    /**
     * Is an action defined
     * 
     * @param string  $name
     * 
     * @return boolean
     */
    public function has(string $name): bool
    {
        return isset($this->actions[$name]);
    }

    /**
     * Remove an action
     * 
     * @param string $name
     * 
     * @return Actions
     */
    public function remove(string $name): bool
    {
        if ($this->has($name)) {
            unset($this->actions[$name]);
        }
        return $this;
    }

    /**
     * Get an action
     * 
     * @param string $name
     * 
     * @return ?array
     */
    public function get($name): ?array
    {
        return $this->actions[$name] ?? null;
    }

    /**
     * Get all actions
     * 
     * @return array
     */
    public function all(): array
    {
        return $this->actions;
    }

    /**
     * Build all the action for an object, checking access
     * 
     * @param object $object
     * 
     * @return array
     */
    public function make($object): array
    {
        $actions = $this->actions;
        event(new ActionsRetrieved($actions, $object));
        foreach ($actions as $name => $action) {
            if (isset($action['access']) and !$action['access']($object)) {
                unset($actions[$name]);
                continue;
            }
            $actions[$name]['url'] = $action['url']($object);
        }
        return $actions;
    }
}