<?php 

namespace Pingu\Core\Events;

use Illuminate\View\View;
use Pingu\Core\Contracts\RendererContract;

class Rendered
{
    /**
     * @var RendererContract
     */
    public $renderer;

    /**
     * Object being rendered
     */
    public $object;

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
    public function __construct(string &$html, View $view, RendererContract $renderer, $object)
    {
        $this->view = $view;
        $this->html = &$html;
        $this->renderer = $renderer;
        $this->object = $object;
    }
}