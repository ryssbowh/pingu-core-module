<?php 

namespace Pingu\Core\Events;

use Pingu\Core\Contracts\RendererContract;
use Pingu\Entity\Entities\ViewMode;

class Rendering
{
    /**
     * @var RendererContract
     */
    public $renderer;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(RendererContract $renderer)
    {
        $this->renderer = $renderer;
    }
}