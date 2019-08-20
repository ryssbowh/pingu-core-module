<?php

namespace Pingu\Core\Traits\Controllers;

use Pingu\Core\Entities\BaseModel;
use Pingu\Forms\Support\Form;

trait EditsAjaxModel 
{
	use EditsModel;

	/**
	 * @inheritDoc
	 */
	protected function onEditFormCreated(Form $form, BaseModel $model)
	{	
		return ['form' => $form->renderAsString()];
	}

	/**
	 * @inheritDoc
	 */
	protected function afterEditFormCreated(Form $form, BaseModel $model)
	{
		$form->addViewSuggestion('forms.modal')
			->addClass('js-ajax-form')
			->option('title', 'Edit a '.$model::friendlyName());
	}

	/**
	 * @inheritDoc
	 */
	protected function getUpdateUriPrefix()
	{
		return ajaxPrefix();
	}
}
