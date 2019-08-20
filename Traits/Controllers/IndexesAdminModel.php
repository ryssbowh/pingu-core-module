<?php

namespace Pingu\Core\Traits\Controllers;

use Illuminate\Database\Eloquent\Collection;

trait IndexesAdminModel 
{
	use IndexesModel;

	/**
	 * @inheritDoc
	 */
	protected function onIndexSuccess(Collection $models)
	{
		return $this->getIndexView($models);
	}

	/**
	 * Get the view for a create request
	 * 
	 * @param  Collection $models
	 * @return view
	 */
	protected function getIndexView(Collection $models)
	{
		$with = [
			'total' => $models->count(),
			'models' => $models,
		];
		$this->addVariablesToIndexView($with);
		return view($this->getIndexViewName())->with($with);
	}

	/**
	 * View name for creating models
	 * 
	 * @return string
	 */
	protected function getIndexViewName()
	{
		return 'pages.indexModel';
	}

	/**
	 * Callback to add variables to the view
	 * 
	 * @param array &$with
	 */
	protected function addVariablesToIndexView(array &$with){}
}
