<?php

namespace Pingu\Core\Contracts;

interface RenderableContract
{   
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