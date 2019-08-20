<?php

namespace Pingu\Core\Traits\Controllers;

use Pingu\Forms\Support\Form;

trait CreatesAdminModel
{
	use CreatesModel;

	/**
	 * @inheritDoc
	 */
	protected function onCreateFormCreated(Form $form)
	{
		return $this->getCreateView($form);
	}

	/**
	 * @inheritDoc
	 */
	protected function afterCreateFormCreated(Form $form){
		$form->addBackButton();
	}

	/**
	 * Get the view for a create request
	 * 
	 * @param  Form $form
	 * @param  string $model
	 * @return view
	 */
	protected function getCreateView(Form $form)
	{
		$with = [
			'form' => $form,
			'model' => $this->model,
		];
		$this->addVariablesToCreateView($with);
		return view($this->getCreateViewName())->with($with);
	}

	/**
	 * View name for creating models
	 * 
	 * @return string
	 */
	protected function getCreateViewName()
	{
		return 'pages.addModel';
	}

	/**
	 * Callback to add variables to the view
	 * 
	 * @param array &$with
	 */
	protected function addVariablesToCreateView(array &$with){}

	/**
	 * @inheritDoc
	 */
	protected function getStoreUriPrefix()
	{
		return adminPrefix();
	}

}
