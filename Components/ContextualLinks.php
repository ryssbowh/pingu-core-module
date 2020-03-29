<?php
namespace Pingu\Core\Components;

use Pingu\Core\Contracts\HasActionsContract;
use Pingu\Entity\Support\Entity;
use Request;

class ContextualLinks
{
    public $links = [];
    public $activeLink = null;

    public function addLinks(array $links)
    {
        foreach($links as $name => $link){
            $this->addLink($name, $link);
        }
    }

    public function addLink(string $name, array $link)
    {
        if (isset($this->links[$name])) {
            throw new Exception('Contextual link '.$name.' already exists');
        }
        $this->links[$name] = $link;
        $url = ltrim(trim($link['url'], '/'), '/');
        if (Request::path() == $url) {
            $this->setActiveLink($name);
        }
    }

    public function get($name = null)
    {
        if (is_null($name)) {
            return $this->links;
        }
        return $this->links[$name] ?? null;
    }

    public function setActiveLink($name)
    {
        $this->activeLink = $name;
    }

    public function getActiveLink()
    {
        return $this->activeLink;
    }

    public function addFromObject(HasActionsContract $object)
    {
        $links = $object::actions()->make($object);
        $this->addLinks($links);
    }
}