<?php

namespace Pingu\Core\Support;

use Closure;
use Illuminate\Support\Arr;
use Pingu\Core\Exceptions\ActionsException;

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

    /**
     * Actions defined for this model
     * Each action must define the following
     * 'name' => [
     *     'label' => 'label'
     *     'url' => Closure,
     *     'scope' => '*' or array,
     *     'access' => Closure($model) (optionnal)
     * ]
     * 
     * @return [type] [description]
     */
    abstract protected function actions(): array;

    /**
     * Add an action
     * 
     * @param string       $name
     * @param string       $label
     * @param string|array $scopes
     * @param Closure      $url
     * @param Closure|null $accessCallback
     *
     * @throws ActionsException
     *
     * @return Actions
     */
    public function add(string $name, string $label, Closure $url, $scopes = '*', ?Closure $accessCallback = null)
    {
        if ($this->has($name)) {
            throw ActionsException::defined($name, $this);
        }
        return $this->replace($name, $label, $url, $scopes, $accessCallback);
    }

    /**
     * Adds an action (no checks)
     * 
     * @param string       $name
     * @param string       $label
     * @param string|array $scopes
     * @param Closure      $url
     * @param Closure|null $accessCallback
     * 
     * @return Actions
     */
    public function replace(string $name, string $label, Closure $url, $scopes = '*', ?Closure $accessCallback = null)
    {
        $this->actions[$name] = [
            'label' => $label,
            'url' => $url,
            'scope' => Arr::wrap($scopes),
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
            $this->add($name, $action['label'], $action['url'], $action['scope'] ?? '*', $action['access'] ?? null);
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
            $this->replace($name, $action['label'], $action['url'], $action['scope'] ?? '*', $action['access'] ?? null);
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
     * @param string|array $scope
     * 
     * @return array
     */
    public function make($object, $scope = '*'): array
    {
        $actions = [];
        foreach (array_keys($this->actions) as $name) {
            if (is_null($built = $this->makeOne($object, $name, $scope))) {
                unset($actions[$name]);
                continue;
            }
            $actions[$name] = $built;
        }
        return $actions;
    }

    /**
     * Builds one action. Returns null if the action is not accessible
     * 
     * @param object $object
     * @param string $action
     * @param string|array $scopes
     *
     * @throws ActionsException
     * 
     * @return ?array
     */
    public function makeOne($object, string $action, $scopes = '*'): ?array
    {
        if (!$this->has($action)) {
            throw ActionsException::undefined($action, $object);
        }
        $action = $this->actions[$action];
        if (!$this->actionIsInScope($action, Arr::wrap($scopes))) {
            return null;
        }
        if (isset($action['access']) and !$action['access']($object)) {
            return null;
        }
        $action['url'] = $action['url']($object, );
        return $action;
    }

    /**
     * Determine if an action is in a array of scopes
     * 
     * @param array  $action
     * @param array  $scopes
     * 
     * @return bool
     */
    protected function actionIsInScope(array $action, array $scopes)
    {
        if (in_array('*', $action['scope']) or in_array('*', $scopes)) {
            return true;
        }
        foreach ($scopes as $scope) {
            if (in_array($scope, $action['scope'])) {
                return true;
            }
        }
        return false;
    }
}