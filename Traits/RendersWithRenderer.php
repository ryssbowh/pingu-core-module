<?php 

namespace Pingu\Core\Traits;

trait RendersWithRenderer
{  
    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return $this->getRenderer()->render();
    }
}