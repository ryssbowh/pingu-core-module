<?php

namespace Pingu\Core\Traits\Controllers;

use Pingu\Core\Contracts\Models\HasContextualLinksContract;
use Pingu\Core\Entities\BaseModel;
use Pingu\Forms\Support\Form;

trait EditsAdminModel
{
	use EditsModel;

	/**
	 * Return the view for an edit request
	 * @param  Form $form
	 * @param  BaseModel $model 
	 * @return view
	 */
	protected function onEditFormCreated(Form $form, BaseModel $model)
	{
		if($model instanceof HasContextualLinksContract){
			\ContextualLinks::addModelLinks($model);
		}
		$with = [
			'form' => $form,
			'model' => $model,
		];
		$this->addVariablesToEditView($with, $model);
		return view($this->getEditViewName($model))->with($with);
	}

	/**
	 * @inheritDoc
	 */
	protected function afterEditFormCreated(Form $form, BaseModel $model){
		$form->addBackButton();
	}

	/**
	 * View name for editing models
	 * 
	 * @return string
	 */
	protected function getEditViewName(BaseModel $model)
	{
		return 'pages.editModel';
	}

	/**
	 * Adds variables to the edit view
	 * 
	 * @param array     &$with
	 * @param Basemodel $model
	 */
	protected function addVariablesToEditView(array &$with, BaseModel $model){}

	/**
	 * @inheritDoc
	 */
	protected function getUpdateUriPrefix()
	{
		return adminPrefix();
	}

}
