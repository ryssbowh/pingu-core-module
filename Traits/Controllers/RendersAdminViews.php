<?php

namespace Pingu\Core\Traits\Controllers;

use Pingu\Core\Renderers\AdminViewRenderer;

trait RendersAdminViews
{
    /**
     * Renders an admin view
     * 
     * @param view|array $views
     * @param string $identifier
     * @param array  $data
     * 
     * @return string
     */
    protected function renderAdminView($views, string $identifier, array $data): string
    {
        $renderer = new AdminViewRenderer($views, \Str::camel($identifier), $data);
        return $renderer->render();
    }
} 