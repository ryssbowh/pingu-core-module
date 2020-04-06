<?php

namespace Pingu\Core\Contracts;

use Illuminate\Support\Collection;

interface RendererContract
{
    /**
     * Render
     *
     * @param ?viewMode
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
     * Removes a view from the list
     * 
     * @param string $name
     */
    public function removeView(string $name);

    /**
     * Data getter
     * 
     * @return Collection|mixed
     */
    public function getData(?string $name);

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
     * @param Collection $data
     *
     * @return Renderer
     */
    public function replaceData(Collection $data);

    /**
     * Is a data set
     * 
     * @param string  $name
     * 
     * @return boolean
     */
    public function hasData(string $name): bool;
}