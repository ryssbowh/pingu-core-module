<?php

namespace Pingu\Core\Support\Uris;

use Pingu\Core\Contracts\HasRouteSlugContract;
use Pingu\Core\Support\Uris\Uris;
use Pingu\Entity\Support\Entity;

class BaseModelUris extends Uris
{
    protected $model;

    /**
     * @inheritDoc
     */
    protected $uris = [
        'index' => '@slugs@',
        'view' => '@slugs@/{@slug@}',
        'create' => '@slugs@/create',
        'store' => '@slug@',
        'confirmDelete' => '@slugs@/{@slug@}/delete',
        'delete' => '@slugs@/{@slug@}/delete',
        'edit' => '@slugs@/{@slug@}/edit',
        'update' => '@slugs@/{@slug@}',
        'patch' => '@slugs@'
    ];

    public function __construct(string $model)
    {
        $this->model = $model;
        $this->replaceMany($this->uris);
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function replacableSlugs(): array
    {
        return [
            '@slugs@' => $this->model::routeSlugs(),
            '@slug@' => $this->model::routeSlug()
        ];
    }

    /**
     * @inheritDoc
     * 
     * @return array
     */
    protected function uris(): array
    {
        return [];
    }
}