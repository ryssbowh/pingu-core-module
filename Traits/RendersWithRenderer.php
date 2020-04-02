<?php 

namespace Pingu\Core\Traits;

use Pingu\Core\Contracts\RendererContract;

trait RendersWithRenderer
{  
    /**
     * Get renderer for this object
     * 
     * @return RendererContract
     */
    public abstract function getRenderer(): RendererContract;

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return $this->getRenderer()->render();
    }
}