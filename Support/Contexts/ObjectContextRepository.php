<?php

namespace Pingu\Core\Support\Contexts;

use Pingu\Core\Contracts\RouteContexts\RouteContextContract;
use Pingu\Core\Contracts\RouteContexts\RouteContextRepositoryContract;
use Pingu\Core\Entities\BaseModel;
use Pingu\Core\Exceptions\ContextException;
use Pingu\Core\Http\Contexts\CreateContext;
use Pingu\Core\Http\Contexts\DeleteContext;
use Pingu\Core\Http\Contexts\EditContext;
use Pingu\Core\Http\Contexts\IndexContext;
use Pingu\Core\Http\Contexts\PatchContext;
use Pingu\Core\Http\Contexts\StoreContext;
use Pingu\Core\Http\Contexts\UpdateContext;

class ObjectContextRepository implements RouteContextRepositoryContract
{
    /**
     * @var array
     */
    protected $contexts = [
        'index' => IndexContext::class,
        'create' => CreateContext::class,
        'store' => StoreContext::class,
        'edit' => EditContext::class,
        'update' => UpdateContext::class,
        'patch' => PatchContext::class,
        'delete' => DeleteContext::class,
    ];

    public function __construct(array $contexts)
    {
        $this->addMany($contexts);
    }

    /**
     * @inheritDoc
     */
    public function add(string $context): RouteContextRepositoryContract
    {
        $this->contexts[$context::scope()] = $context;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addMany(array $contexts): RouteContextRepositoryContract
    {
        foreach ($contexts as $context) {
            $this->add($context);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function has(string $scope): bool
    {
        return isset($this->contexts[$scope]);
    }

    /**
     * @inheritDoc
     */
    public function get(object $object, string $scope): RouteContextContract
    {
        if (!$this->has($scope)) {
            throw ContextException::undefined($scope, $object);
        }
        if (is_string($context = $this->contexts[$scope])) {
            $this->contexts[$scope] = new $context($object);
        }
        return $this->contexts[$scope];
    }

    /**
     * @inheritDoc
     */
    public function getFirst(object $object, array $scopes): RouteContextContract
    {
        foreach ($scopes as $scope) {
            if ($this->has($scope)) {
                return $this->get($object, $scope);
            }
        }
        throw ContextException::nothingDefined($object, $scopes);
    }
}