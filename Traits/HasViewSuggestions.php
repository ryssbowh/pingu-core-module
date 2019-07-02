<?php

namespace Pingu\Core\Traits;

trait HasViewSuggestions
{
	protected $viewSuggestions;

	protected function setViewSuggestions(array $suggestions)
	{
		$this->viewSuggestions = $suggestions;
	}

	public function addViewSuggestion($suggestions)
	{
		if(is_array($suggestions)){
			return $this->addViewSuggestions($suggestions);
		}
		if(!$this->hasViewSuggestions($suggestions)){
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
		foreach($suggestions as $suggestion){
			$this->addViewSuggestion($suggestion);
		}
		return $this;
	}

	public function removeViewSuggestion(string $suggestion)
	{
		if($this->hasViewSuggestions($suggestion)){
			unset($this->viewSuggestions[array_search($suggestion, $this->viewSuggestions)]);
		}
		return $this;
	}

	public function hasViewSuggestions(string $suggestion)
	{
		return in_array($suggestion, $this->viewSuggestions);
	}

}