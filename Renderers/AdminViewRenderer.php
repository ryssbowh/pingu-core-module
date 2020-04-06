<?php 

namespace Pingu\Core\Renderers;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Pingu\Core\Support\Renderers\BaseRenderer;

class AdminViewRenderer extends BaseRenderer
{
    protected $identifier;

    public function __construct($views, string $identifier, array $data)
    {
        $this->views = Arr::wrap($views);
        $this->data = collect($data);
        $this->identifier = $identifier;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultViews(): array
    {
        return $this->views;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultData(): Collection
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function viewFolder(): string
    {
        return 'pages';
    }

    /**
     * @inheritDoc
     */
    public function getHookName(): string
    {
        return 'adminPage';
    }

    /**
     * @inheritDoc
     */
    public function viewIdentifier(): string
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