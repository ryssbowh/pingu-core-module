<?php

namespace Pingu\Core\Support;

use Pingu\Core\Entities\BaseModel;
use Pingu\Core\Http\Controllers\BaseController;
use Pingu\Core\Traits\Controllers\HasRouteContexts;

abstract class ModelCrudContextController extends BaseController
{
    use HasRouteContexts;

    /**
     * Get model associated to a create/store/index/patch request
     * 
     * @return BaseModel
     */
    abstract protected function getModel(): BaseModel;

    /**
     * Action that doesn't require validation (create/edit/index/delete)
     *
     * @param ?BaseModel $model
     * 
     * @return mixed
     */
    protected function nonValidatableAction(?BaseModel $model = null)
    {
        $model = $model ?? $this->getModel();

        $context = $this->getRouteContext($model, $this->request);
        
        return $context->getResponse($model);
    }

    /**
     * Action that requires validation (store/update/patch)
     *
     * @param ?BaseModel $model
     * 
     * @return mixed
     */
    protected function validatableAction(?BaseModel $model = null)
    {
        $model = $model ?? $this->getModel();

        $context = $this->getValidatableContext($model, $this->request);
        
        return $context->getResponse($model);
    }

    /**
     * Create action
     * 
     * @return mixed
     */
    public function create()
    {   
        return $this->nonValidatableAction();
    }

    /**
     * Store action
     * 
     * @return mixed
     */
    public function store()
    {
        return $this->validatableAction();
    }

    /**
     * Index action
     * 
     * @return mixed
     */
    public function index()
    {
        return $this->nonValidatableAction();
    }

    /**
     * Edit action
     * 
     * @return mixed
     */
    public function edit(BaseModel $model)
    {
        return $this->nonValidatableAction($model);
    }

    /**
     * Update action
     * 
     * @return mixed
     */
    public function update(BaseModel $model)
    {
        return $this->validatableAction($model);
    }

    /**
     * Delete action
     * 
     * @return mixed
     */
    public function delete(BaseModel $model)
    {
        return $this->nonValidatableAction($model);
    }

    /**
     * Patch action
     * 
     * @return mixed
     */
    public function patch()
    {
        return $this->validatableAction();
    }
}