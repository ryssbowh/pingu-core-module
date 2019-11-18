<?php

namespace Pingu\Core\Traits;

trait RendersWithSuggestions
{
    protected $viewSuggestions = [];

    protected abstract function getViewData(): array;

    protected function setViewSuggestions(array $suggestions)
    {
        $this->viewSuggestions = $suggestions;
    }

    public function addViewSuggestion($suggestions)
    {
        if (is_array($suggestions)) {
            return $this->addViewSuggestions($suggestions);
        }

        if (!$this->hasViewSuggestions($suggestions)) {
            array_unshift($this->viewSuggestions, $suggestions);
        }
        return $this;
    }

    public function getViewSuggestions()
    {
        return $this->viewSuggestions;
    }

    public function addViewSuggestions(array $suggestions)
    {
        $suggestions = array_reverse($suggestions);
        foreach ($suggestions as $suggestion) {
            $this->addViewSuggestion($suggestion);
        }
        return $this;
    }

    public function removeViewSuggestion(string $suggestion)
    {
        if ($this->hasViewSuggestions($suggestion)) {
            unset($this->viewSuggestions[array_search($suggestion, $this->viewSuggestions)]);
        }
        return $this;
    }

    public function hasViewSuggestions(string $suggestion)
    {
        return in_array($suggestion, $this->viewSuggestions);
    }

    public function render()
    {
        echo $this->__toString();
    }

    public function __toString()
    {
        $data = $this->getViewData();
        return view()->first($this->getViewSuggestions(), $data)->render();
    }

}