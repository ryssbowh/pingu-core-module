<?php

namespace Pingu\Core\Support\Renderers;

use Illuminate\Support\Traits\ForwardsCalls;
use Pingu\Core\Contracts\RenderableContract;
use Pingu\Entity\Entities\ViewMode;

abstract class ObjectRenderer extends BaseRenderer
{
    /**
     * Object being rendered
     * @var object
     */
    protected $object;

    public function __construct(RenderableContract $object)
    {
        $this->object = $object;
        parent::__construct();
    }

    /**
     * Base folder for the views
     * 
     * @return string
     */
    protected abstract function viewFolder(): string;

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
    public function getHookData(): array
    {
        return [$this->viewKey(), $this->object, $this];
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultViews(): array
    {
        return [
            $this->viewFolder().'.'.$this->viewIdentifier().'_'.$this->viewKey(),
            $this->viewFolder().'.'.$this->viewIdentifier(),
            $this->object->systemView()
        ];
    }

    /**
     * Base identifier for all view names
     * 
     * @return string
     */
    protected function viewIdentifier(): string
    {
        return $this->object->viewIdentifier();
    }

    /**
     * Key to build view names
     * 
     * @return string
     */
    protected function viewKey(): string
    {
        return $this->object->getViewKey();
    }
}