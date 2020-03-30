<?php 

namespace Pingu\Core\Events;

use Pingu\Core\Contracts\RendererContract;

class Rendering
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
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(RendererContract $renderer, $object)
    {
        $this->renderer = $renderer;
        $this->object = $object;
    }
}