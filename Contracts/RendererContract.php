<?php

namespace Pingu\Core\Contracts;

interface RendererContract
{
    /**
     * Object being rendered
     * 
     * @return object
     */
    public function getObject();
    
    /**
     * Render
     * 
     * @return string
     */
    public function render(): string;

    /**
     * Views getter
     * 
     * @return array
     */
    public function getViews(): array;

    /**
     * Does this renderer have a view
     * 
     * @param string  $name
     * 
     * @return boolean
     */
    public function hasView(string $name): bool;

    /**
     * Views setter
     * 
     * @param array $views
     *
     * @return Renderer
     */
    public function setViews(array $views);

    /**
     * Prepend a view to the list
     * 
     * @param string $name
     * 
     * @return Renderer
     */
    public function prependView(string $name);

    /**
     * Data getter
     * 
     * @return array
     */
    public function getData(): array;

    /**
     * Data setter
     * 
     * @param string $name
     * @param mixed  $value
     * @param bool   $force
     *
     * @return Renderer
     */
    public function addData(string $name, $value, bool $force = false);

    /**
     * Merge data
     * 
     * @param array $data        
     * @param bool  $keepCurrent
     * 
     * @return Renderer
     */
    public function mergeData(array $data, bool $keepCurrent = false);

    /**
     * Data setter
     * 
     * @param array $data
     *
     * @return Renderer
     */
    public function setData(string $name, $value);

    /**
     * Replaces all data
     * 
     * @param array $data
     *
     * @return Renderer
     */
    public function replaceData(array $data);

    /**
     * Identifier for this renderer, will be caught by the Theme hooks
     * 
     * @return string
     */
    public function identifier(): string;

    /**
     * Identifier for this renderer's object, will be caught by the Theme hooks
     * 
     * @return string
     */
    public function objectIdentifier(): string;

    /**
     * Data to be sent to the hook
     * 
     * @return array
     */
    public function getHookData(): array;
}