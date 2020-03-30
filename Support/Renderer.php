<?php

namespace Pingu\Core\Support;

use Pingu\Core\Contracts\RendererContract;
use Pingu\Core\Events\Rendered;
use Pingu\Core\Events\Rendering;

abstract class Renderer implements RendererContract
{
    /**
     * Object being rendered
     * @var object
     */
    protected $object;

    /**
     * Candidate view names
     * @var array
     */
    protected $views = [];

    /**
     * Data for the view
     * @var array
     */
    protected $data = [];

    public function __construct(object $object)
    {
        $this->object = $object;
        $this->views = $this->getDefaultViews();
        $this->data = $this->getDefaultData();
    }

    /**
     * Default views
     * 
     * @return array
     */
    abstract public function getDefaultViews(): array;

    /**
     * Default data
     * 
     * @return array
     */
    abstract public function getDefaultData(): array;

    /**
     * @inheritDoc
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $this->beforeRendering();
        event(new Rendering($this, $this->object));
        $view = view()->first($this->getViews(), $this->getFinalData());
        $html = $view->render();
        event(new Rendered($html, $view, $this, $this->object));
        return $html;
    }

    /**
     * @inheritDoc
     */
    public function getViews(): array
    {
        return $this->views;
    }

    /**
     * @inheritDoc
     */
    public function setViews(array $views)
    {
        $this->views = $views;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hasView(string $name): bool
    {
        return in_array($name, $this->views);
    }

    /**
     * @inheritDoc
     */
    public function prependView(string $name)
    {
        $this->removeView($name);
        array_unshift($this->views, $name);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function addData(string $name, $value, bool $force = false)
    {
        if (isset($this->data[$name]) and !$force) {
            return $this;
        }
        $this->data[$name] = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function mergeData(array $data, bool $keepCurrent = false)
    {
        if ($keepCurrent) {
            $this->data = array_merge($data, $this->data);
        } else {
            $this->data = array_merge($this->data, $data);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Removes a view from the list
     * 
     * @param string $name
     */
    protected function removeView(string $name)
    {
        if ($this->hasView($name)) {
            $index = array_search($name, $this->views);
            unset($this->views[$index]);
        }
    }

    /**
     * Get final data to be sent to the view
     * 
     * @return array
     */
    protected function getFinalData(): array
    {
        return $this->getData();
    }

    /**
     * Called before rendering
     */
    protected function beforeRendering(){}

}