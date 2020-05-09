<?php

namespace Pingu\Core\Components;

use Pingu\Core\Contracts\ActionRepositoryContract;
use Pingu\Core\Contracts\HasActionsContract;
use Pingu\Core\Exceptions\ActionsException;

class Actions
{
    /**
     * @var array
     */
    protected $actionsRepos = [];

    /**
     * Registers an action class
     * 
     * @param string         $class
     * @param ActionRepositoryContract|string $actions
     */
    public function register($model, $actions)
    {
        $model = object_to_class($model);
        $this->actionsRepos[$model] = $actions;
    }

    /**
     * Get an action class for a class
     * 
     * @param string|object $class
     * 
     * @return ActionRepositoryContract
     */
    public function get($model): ActionRepositoryContract
    {
        $model = object_to_class($model);
        if (isset($this->actionsRepos[$model])) {
            $repo = $this->actionsRepos[$model];
            if (is_string($repo)) {
                $repo = new $repo;
                $this->actionsRepos[$model] = $repo;
            }
            return $repo;
        }
        throw ActionsException::undefinedForModel($model);
    }

    /**
     * Get all action classes
     * 
     * @return array
     */
    public function all(): array
    {
        return $this->actionsRepos;
    }
}