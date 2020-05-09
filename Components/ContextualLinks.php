<?php
namespace Pingu\Core\Components;

use Pingu\Core\Contracts\HasActionsContract;
use Pingu\Entity\Support\Entity;
use Request;

class ContextualLinks
{
    public $links = [];
    public $activeLink = null;

    /**
     * Add many links
     * 
     * @param array $links
     */
    public function addLinks(array $links)
    {
        foreach($links as $name => $link){
            $this->addLink($name, $link);
        }
    }

    /**
     * Add a single link
     * 
     * @param string $name
     * @param array  $link
     */
    public function addLink(string $name, array $link)
    {
        if (isset($this->links[$name])) {
            throw new Exception('Contextual link '.$name.' already exists');
        }
        $this->links[$name] = $link;
        $url = '/'.trim($link['url'], '/');
        if (Request::getRequestUri() == $url) {
            $this->setActiveLink($name);
        } else if ('/'.Request::path() == $url) {
            $this->setActiveLink($name);
        }
    }

    /**
     * Get a link or all links
     * 
     * @param ?string $name
     * 
     * @return array
     */
    public function get(?string $name = null)
    {
        if (is_null($name)) {
            return $this->links;
        }
        return $this->links[$name] ?? null;
    }

    /**
     * Set the active link
     * 
     * @param string $name
     */
    public function setActiveLink(string $name)
    {
        $this->activeLink = $name;
    }

    /**
     * Get the active link
     * 
     * @return ?string
     */
    public function getActiveLink(): ?string
    {
        return $this->activeLink;
    }

    /**
     * Add links from an object's actions class
     * 
     * @param HasActionsContract $object
     * @param string $prefix
     */
    public function addObjectActions(HasActionsContract $object, string $scope = '*')
    {
        $links = $object::actions()->make($object, $scope);
        $this->addLinks($links);
    }
}