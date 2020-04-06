<?php

namespace Pingu\Core\Support\Renderers;

use Illuminate\Support\Collection;
use Pingu\Core\Contracts\RenderableContract;
use Pingu\Core\Contracts\RendererContract;
use Pingu\Core\Events\Rendered;
use Pingu\Core\Events\Rendering;
use Pingu\Entity\Entities\ViewMode;

abstract class BaseRenderer implements RendererContract
{
    /**
     * Candidate view names
     * @var array
     */
    protected $views = [];

    /**
     * Data for the view
     * @var Collection
     */
    protected $data;

    public function __construct()
    {
        $this->views = $this->getDefaultViews();
        $this->data = $this->getDefaultData();
    }

    /**
     * Base folder for the views
     * 
     * @return string
     */
    protected abstract function viewFolder(): string;

    /**
     * Defualt data for this renderer
     * 
     * @return Collection
     */
    protected abstract function getDefaultData(): Collection;

    /**
     * Data to send to the hook
     * 
     * @return array
     */
    protected abstract function getHookData(): array;

    /**
     * Hook to be called before rendering
     * 
     * @return string
     */
    protected abstract function getHookName(): string;

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $this->callThemeHook();
        return $this->performRender();
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
    public function getData(?string $name = null)
    {
        if (!is_null($name)) {
            return $this->data->get($name, null);
        }
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function addData(string $name, $value, bool $force = false)
    {
        if ($this->hasData($name) and !$force) {
            return $this;
        }
        $this->data->put($name, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function mergeData(array $data, bool $keepCurrent = false)
    {
        if ($keepCurrent) {
            $this->data = collect($data)->merge($this->data);
        } else {
            $this->data = $this->data->merge($data);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setData(string $name, $value)
    {
        $this->addData($name, $value, true);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function replaceData(Collection $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hasData(string $name): bool
    {
        return $this->data->has($name);
    }

    /**
     * @inheritDoc
     */
    public function removeView(string $name)
    {
        if ($this->hasView($name)) {
            $index = array_search($name, $this->views);
            unset($this->views[$index]);
        }
    }

    /**
     * Call the theme hook
     */
    protected function callThemeHook()
    {
        $this->beforeThemeHook();
        \ThemeHooks::dispatch($this->getHookName(), $this->getHookData());
        $this->afterThemeHook();
    }

    /**
     * Do the actual view rendering
     * 
     * @return string
     */
    protected function performRender(): string
    {
        event(new Rendering($this));
        $view = view()->first($this->getViews(), $this->getData()->all());
        $html = $view->render();
        event(new Rendered($html, $view, $this));
        return $html;
    }

    /**
     * Callback before calling the theme hook
     */
    protected function beforeThemeHook(){}

    /**
     * Callback after calling the theme hook
     */
    protected function afterThemeHook(){}

    /**
     * Forward setter to this ernderer's data
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        return $this->setData($name, $value);
    }

    /**
     * Forward getter to this renderer's data
     * @param $name
     * @param $value
     * 
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getData($name);
    }
}