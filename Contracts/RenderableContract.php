<?php

namespace Pingu\Core\Contracts;

interface RenderableContract
{   
    /**
     * Renderer for this object
     * 
     * @return RendererContract
     */
    public function getRenderer(): RendererContract;

    /**
     * Renders this object
     * 
     * @return string
     */
    public function render(): string;
}