<?php

namespace Pingu\Core\Support\Actions;

use Closure;
use Illuminate\Support\Arr;
use Pingu\Core\Contracts\ActionContract;
use Pingu\Core\Entities\BaseModel;
use Pingu\Core\Exceptions\ActionsException;

class BaseAction implements ActionContract
{
    /**
     * @var string
     */
    protected $label;

    /**
     * @var Closure
     */
    protected $url;

    /**
     * @var Closure
     */
    protected $access;

    /**
     * @var array
     */
    protected $scopes;

    public function __construct(string $label, \Closure $url, \Closure $access, $scopes = ['*'])
    {
        $this->label = $label;
        $this->access = $access;
        $this->url = $url;
        $this->scopes = Arr::wrap($scopes);
    }

    /**
     * @inheritDoc
     */
    public function getUrl(object $object): string
    {
        $url = $this->url;
        return $url($object);
    }

    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @inheritDoc
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    /**
     * @inheritDoc
     */
    public function checkAccess(object $object): bool
    {
        $closure = $this->access;
        return $closure($object);
    }

    /**
     * @inheritDoc
     */
    public function toArray(object $object): array
    {
        return [
            'label' => $this->label,
            'url' => $this->getUrl($object)
        ];
    }

    /**
     * @inheritDoc
     */
    public function isInScope($scopes): bool
    {
        $scopes = Arr::wrap($scopes);
        if (in_array('*', $this->scopes) or in_array('*', $scopes)) {
            return true;
        }
        foreach ($scopes as $scope) {
            if (in_array($scope, $this->scopes)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function hasScope(string $name): bool
    {
        return in_array($name, $this->scopes);
    }

    /**
     * @inheritDoc
     */
    public function addScope(string $name): ActionContract
    {
        if (!$this->hasScope($name)) {
            $this->scopes[] = $name;
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeScope(string $name): ActionContract
    {
        if ($this->hasScope($name)) {
            $index = array_search($name, $this->scopes);
            unset($this->scopes[$name]);
        }
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setAccess(\Closure $closure): ActionContract
    {
        $this->access = $closure;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setUrl(\Closure $closure): ActionContract
    {
        $this->url = $closure;
        return $this;
    }
}