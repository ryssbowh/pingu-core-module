<?php

class ViewSuggestions
{
    protected $suggestions = [];

    protected $cacheKey;

    public function __construct()
    {
        $this->cacheKey = config('core.views.suggestionsCacheKey');
    }

    protected function isNamespacedView(string $name)
    {
        return is_int(strpos('::', $name));
    }

    public function forEntity()
    {

    }

    public function forForm()
    {

    }

    public function forField()
    {

    }

    public function forEntities(string $default, string $entityClass)
    {

    }
}