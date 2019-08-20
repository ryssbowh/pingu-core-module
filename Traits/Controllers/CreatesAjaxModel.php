<?php

namespace Pingu\Core\Traits\Controllers;

use Pingu\Forms\Support\Form;

trait CreatesAjaxModel 
{
	use CreatesModel;

	/**
	 * @inheritDoc
	 */
	protected function onCreateFormCreated(Form $form)
	{	
		return ['form' => $form->renderAsString()];
	}

	/**
	 * @inheritDoc
	 */
	protected function afterCreateFormCreated(Form $form)
	{
		$form->addViewSuggestion('forms.modal')
			->addClass('js-ajax-form')
			->option('title', 'Add a '.$this->model::friendlyName());
	}

	/**
	 * @inheritDoc
	 */
	protected function getStoreUriPrefix()
	{
		return ajaxPrefix();
	}
}