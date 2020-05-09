<?php

namespace Pingu\Core\Contracts;

interface ActionContract
{
    /**
     * Scopes getter
     * 
     * @return array
     */
    public function getScopes(): array;

    /**
     * Url getter 
     *
     * @param object $object
     * 
     * @return string
     */
    public function getUrl(object $object): string;

    /**
     * Check access
     *
     * @param object $object
     * 
     * @return bool
     */
    public function checkAccess(object $object): bool;

    /**
     * Label getter
     * 
     * @return string
     */
    public function getLabel(): string;

    /**
     * Is the action in scope
     * 
     * @param array|string $scopes
     * 
     * @return bool
     */
    public function isInScope($scopes): bool;

    /**
     * @param object $object
     * 
     * @return array
     */
    public function toArray(object $object): array;

    /**
     * Does this has a scope
     * 
     * @param  string  $name
     * 
     * @return boolean
     */
    public function hasScope(string $name): bool;

    /**
     * Adds a scope
     * 
     * @param string $name
     *
     * @return ActionContract
     */
    public function addScope(string $name): ActionContract;

    /**
     * Adds a scope
     * 
     * @param string $name
     *
     * @return ActionContract
     */
    public function removeScope(string $name): ActionContract;
    
    /**
     * Set the access through closure
     * 
     * @param \Closure $closure
     *
     * @return ActionContract
     */
    public function setAccess(\Closure $closure): ActionContract;

    /**
     * Set the url through a closure
     * 
     * @param \Closure $closure
     */
    public function setUrl(\Closure $closure): ActionContract;
}