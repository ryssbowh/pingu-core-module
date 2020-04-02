<?php 

namespace Pingu\Core\Renderers;

use Illuminate\Support\Arr;
use Pingu\Core\Support\Renderer;

class AdminViewRenderer extends Renderer
{
    protected $identifier;

    public function __construct($views, string $identifier, array $data)
    {
        $this->views = Arr::wrap($views);
        $this->data = $data;
        $this->identifier = $identifier;
    }

    /**
     * @inheritDoc
     */
    public function identifier(): string
    {
        return 'admin';
    }

    /**
     * @inheritDoc
     */
    public function objectIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @inheritDoc
     */
    public function getHookData(): array
    {
        return [$this];
    }
}