<?php

namespace Pingu\Core\Support\Renderers;

use Pingu\Core\Contracts\RendererContract;
use Pingu\Core\Events\Rendered;
use Pingu\Core\Events\Rendering;
use Pingu\Core\Support\Renderers\Renderer;
use Pingu\Entity\Entities\ViewMode;

abstract class ViewModeRenderer extends ObjectRenderer
{
    /**
     * View mode
     * @var ViewMode
     */
    protected $viewMode;

    public function __construct(object $object, ViewMode $viewMode)
    {
        $this->viewMode = $viewMode;
        parent::__construct($object);
    }

    /**
     * View mode getter
     * 
     * @return ViewMode
     */
    public function getViewMode(): ViewMode
    {
        return $this->viewMode;
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultViews(): array
    {
        $folder = $this->viewFolder();
        $id = $this->viewIdentifier();
        $key = $this->viewKey();
        return [
            $folder.'.'.$id.'_'.$this->viewMode->machineName.'_'.$key,
            $folder.'.'.$id.'_'.$key,
            $folder.'.'.$id.'_'.$this->viewMode->machineName,
            $folder.'.'.$id,
            $this->object->systemView()
        ];
    }

    /**
     * Resolve a view mode
     * 
     * @param null|string|int|ViewMode $viewMode
     *
     * @return ViewMode
     */
    protected function resolveViewMode($viewMode = null): ViewMode
    {
        if (is_null($viewMode)) {
            return \ViewMode::getDefault();
        } else {
            return \ViewMode::get($viewMode);
        }
    }
}