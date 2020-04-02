<?php 

namespace Pingu\Core\Traits;

use Pingu\Core\Renderers\AdminViewRenderer;

trait RendersAdminViews
{
    protected function renderAdminView($views, string $identifier, array $data)
    {
        $renderer = new AdminViewRenderer($views, \Str::camel($identifier), $data);
        return $renderer->render();
    }
} 