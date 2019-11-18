<?php

namespace Pingu\Core\Support;

use Closure;
use Pingu\Core\Exceptions\ActionsException;
use Pingu\Entity\Events\ActionsRetrieved;

abstract class Actions
{
    protected $actions = [];

    public function __construct()
    {
        $this->actions = $this->actions();
    }

    abstract protected function actions(): array;

    public function add(string $name, string $label, Closure $url, ?Closure $accessCallback = null)
    {
        if ($this->has($name)) {
            throw ActionsException::defined($name, $this);
        }
        return $this->replace($name, $label, $url, $accessCallback);
    }

    public function replace(string $name, string $label, Closure $url, ?Closure $accessCallback = null)
    {
        $this->actions[$name] = [
            'label' => $label,
            'url' => $url,
            'access' => $accessCallback
        ];
        return $this;
    }

    public function addMany(array $actions)
    {
        foreach ($actions as $name => $action) {
            $this->add($name, $action['label'], $action['url'], $action['access'] ?? null);
        }
    }

    public function replaceMany(array $actions)
    {
        foreach ($actions as $name => $action) {
            $this->replace($name, $action['label'], $action['url'], $action['access'] ?? null);
        }
    }

    public function has(string $name): bool
    {
        return isset($this->actions[$name]);
    }

    public function remove(string $name): bool
    {
        if ($this->has($name)) {
            unset($this->actions[$name]);
        }
    }

    public function get($name)
    {
        return $this->actions[$name] ?? null;
    }

    public function all()
    {
        return $this->actions;
    }

    public function make($object): array
    {
        $actions = $this->actions;
        event(new ActionsRetrieved($actions, $object));
        foreach ($actions as $name => $action) {
            if (isset($action['access']) and !$action['access']($object)) {
                unset($actions[$name]);
                continue;
            }
            $actions[$name]['url'] = $action['url']($object);
        }
        return $actions;
    }
}