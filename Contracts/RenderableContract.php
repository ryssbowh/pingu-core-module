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
     * @param null|int|string|ViewMode $viewMode
     * 
     * @return string
     */
    public function render($viewMode = null): string;

    /**
     * Default system view
     * 
     * @return string
     */
    public function systemView(): string;

    /**
     * Identifier to build view names
     * @return string
     */
    public function viewIdentifier(): string;

    /**
     * View key used to build view names
     */
    public function getViewKey(): string;
}