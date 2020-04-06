<?php 

namespace Pingu\Core\Events;

use Illuminate\View\View;
use Pingu\Core\Contracts\RendererContract;
use Pingu\Entity\Entities\ViewMode;

class Rendered
{
    /**
     * @var RendererContract
     */
    public $renderer;

    /**
     * View rendered
     * @var View
     */
    public $view;

    /**
     * Html rendered
     * @var string
     */
    public $html;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string &$html, View $view, RendererContract $renderer)
    {
        $this->view = $view;
        $this->html = &$html;
        $this->renderer = $renderer;
    }
}