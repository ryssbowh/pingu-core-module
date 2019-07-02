<?php

namespace Pingu\Core\Traits\Controllers;

use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait PatchesAjaxModel 
{
	public function patch()
	{
		$post = $this->beforePatch($this->request->post());
		if(!isset($post['models'])){
			throw new HttpException(422, "'models' must be set for a patch request");
		}
		$models = collect();
		foreach($post['models'] as $data){
			if(!isset($data[$this->model->getKeyName()])){
				throw new HttpException(422, "The primary key is not set for ".$this->model::friendlyName());
			}
			try{
				$item = $this->getModel()::findOrFail($data[$this->model->getKeyName()]);
				unset($data[$this->model->getKeyName()]);
				$validated = $item->validateForm($data, array_keys($data), true);
				$item->saveWithRelations($validated);
				$models[] = $item->refresh();
			}
			catch(\Exception $e){
				return $this->onPatchFailure($e);
			}
		}
		$this->afterSuccessfullPatch($models);
		return $this->onSuccessfullPatch($models);
	}

	/**
	 * Before patching. Returns the post array
	 * 
	 * @param  array  $post
	 * @return array
	 */
	protected function beforePatch(array $post){
		return $post;
	}

	/**
	 * Actions after successfull patch
	 * 
	 * @param  Collection $models
	 */
	protected function afterSuccessfullPatch(Collection $models){}

	/**
	 * Actions after successfull patch
	 * 
	 * @param  Collection $models
	 */
	protected function onSuccessfullPatch(Collection $models)
	{
		return ['models' => $models->toArray(), 'message' => $this->model::friendlyNames().' have been saved'];
	}

	protected function onPatchFailure(\Exception $e)
	{
		if(env('APP_ENV') == 'local'){
			throw $e;
		}
		throw new HttpException(422, "Error white patching");
	}
}
