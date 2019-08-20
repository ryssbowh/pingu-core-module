<?php

namespace Pingu\Core\Traits\Controllers;

use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait PatchesModel 
{
	public function patch()
	{
		$post = $this->beforePatch($this->request->post());
		if(!isset($post['models'])){
			return $this->onPatchFailure(new HttpException(422, "'models' must be set for a patch request"));
		}
		$models = collect();
		foreach($post['models'] as $id => $data){
			try{
				$item = $this->getModel()::findOrFail($id);
				$validated = $item->validateRequestValues($data, array_keys($data));
				$item->saveWithRelations($validated);
				$models[] = $item->refresh();
			}
			catch(\Exception $e){
				return $this->onPatchFailure($e);
			}
		}
		$this->afterSuccessfullPatch($models);
		return $this->onPatchSuccess($models);
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
	 * Returns reponse after successfull patch
	 * 
	 * @param  Collection $models
	 * @return  mixed
	 */
	protected function onPatchSuccess(Collection $models){}

	/**
	 * Returns reponse after failed patch
	 * 
	 * @param  \Exception $e
	 */
	protected function onPatchFailure(\Exception $e){
		throw $e;
	}

}
