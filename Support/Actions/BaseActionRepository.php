<?php

namespace Pingu\Core\Support\Actions;

use Closure;
use Illuminate\Support\Arr;
use Pingu\Core\Contracts\ActionContract;
use Pingu\Core\Contracts\ActionRepositoryContract;
use Pingu\Core\Exceptions\ActionsException;

abstract class BaseActionRepository implements ActionRepositoryContract
{
    /**
     * @var array
     */
    protected $actions = [];

    public function __construct()
    {
        $this->addMany($this->actions());
    }

    /**
     * Actions defined for this model, must be an array of ActionContract objects
     * 
     * @return array
     */
    abstract protected function actions(): array;

    /**
     * @inheritDoc
     */
    public function add(string $name, ActionContract $action): ActionRepositoryContract
    {
        if ($this->has($name)) {
            throw ActionsException::defined($name, $this);
        }
        return $this->replace($name, $action);
    }

    /**
     * @inheritDoc
     */
    public function replace(string $name, ActionContract $action): ActionRepositoryContract
    {
        $this->actions[$name] = $action;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addMany(array $actions): ActionRepositoryContract
    {
        foreach ($actions as $name => $action) {
            $this->add($name, $action);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function replaceMany(array $actions): ActionRepositoryContract
    {
        foreach ($actions as $name => $action) {
            $this->replace($name, $action);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function has(string $name): bool
    {
        return isset($this->actions[$name]);
    }

    /**
     * @inheritDoc
     */
    public function remove(string $name): ActionRepositoryContract
    {
        if ($this->has($name)) {
            unset($this->actions[$name]);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(string $name): ActionContract
    {
        if (!$this->has($name)) {
            throw ActionsException::undefined($name, $this);
        }
        return $this->actions[$name];
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->actions;
    }

    /**
     * @inheritDoc
     */
    public function make(object $object, $scope = '*'): array
    {
        $actions = [];
        foreach ($this->actions as $name => $action) {
            if (is_null($action = $this->makeOne($name, $object, $scope))) {
                continue;
            }
            $actions[] = $action;
        }
        return $actions;
    }

    /**
     * @inheritDoc
     */
    public function makeOne(string $name, object $object, $scope = '*'): ?array
    {
        $action = $this->get($name);
        if (!$action->isInScope($scope) or !$action->checkAccess($object)) {
            return null;
        }
        return $action->toArray($object);
    }
}