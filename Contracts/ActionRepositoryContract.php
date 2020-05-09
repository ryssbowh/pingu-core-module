<?php

namespace Pingu\Core\Contracts;

use Pingu\Core\Contracts\ActionContract;
use Pingu\Core\Contracts\ActionRepositoryContract;
use Pingu\Core\Entities\BaseModel;

interface ActionRepositoryContract
{   
    /**
     * Add an action
     * 
     * @param string       $name
     * @param ActionContract $action
     *
     * @return ActionRepositoryContract
     */
    public function add(string $name, ActionContract $action): ActionRepositoryContract;

    /**
     * Adds an action (no checks)
     * 
     * @param string       $name
     * @param ActionContract $action
     * 
     * @return ActionRepositoryContract
     */
    public function replace(string $name, ActionContract $action): ActionRepositoryContract;

    /**
     * Adds many actions
     * 
     * @param array $actions
     *
     * @return ActionRepositoryContract
     */
    public function addMany(array $actions): ActionRepositoryContract;

    /**
     * Replaces many actions
     * 
     * @param array  $actions
     * 
     * @return ActionRepositoryContract
     */
    public function replaceMany(array $actions): ActionRepositoryContract;

    /**
     * Is an action defined
     * 
     * @param string  $name
     * 
     * @return boolean
     */
    public function has(string $name): bool;

    /**
     * Remove an action
     * 
     * @param string $name
     * 
     * @return ActionRepositoryContract
     */
    public function remove(string $name): ActionRepositoryContract;

    /**
     * Get an action
     * 
     * @param string $name
     * 
     * @return ActionContract
     */
    public function get(string $name): ActionContract;

    /**
     * Get all actions
     * 
     * @return array
     */
    public function all(): array;

    /**
     * Build all the actions for a object, checking access
     * 
     * @param BaseModel $object
     * @param string|array $scope
     * 
     * @return array
     */
    public function make(object $object, $scope = '*'): array;

    /**
     * Build one action for a object, checking access
     * 
     * @param string $name
     * @param object $object
     * @param string|array $scope
     * 
     * @return ?array
     */
    public function makeOne(string $name, object $object, $scope = '*'): ?array;
}