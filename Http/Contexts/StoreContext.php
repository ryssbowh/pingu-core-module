<?php

namespace Pingu\Core\Http\Contexts;

use Illuminate\Http\RedirectResponse;
use Pingu\Core\Contracts\RouteContexts\ValidatableContextContract;
use Pingu\Core\Entities\BaseModel;
use Pingu\Core\Support\Contexts\BaseRouteContext;
use Pingu\Field\Contracts\HasFieldsContract;
use Pingu\Field\Support\FieldValidator;

class StoreContext extends BaseRouteContext implements ValidatableContextContract
{
    /**
     * @inheritDoc
     */
    public static function scope(): string
    {
        return 'store';
    }

    /**
     * @inheritDoc
     */
    public function getResponse(BaseModel $model = null)
    {
        try{
            $validated = $this->validateRequest();
            $this->performStore($validated);
        }
        catch(\Exception $e){
            return $this->onFailure($e);
        }

        if ($this->wantsJson()) {
            return $this->jsonResponse();
        }
        $this->notify();
        return $this->redirect();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRules(HasFieldsContract $model): array
    {
        return $model->fieldRepository()->validationRules()->toArray();
    }

    /**
     * @inheritDoc
     */
    public function getValidationMessages(HasFieldsContract $model): array
    {
        return $model->fieldRepository()->validationMessages()->toArray();
    }

    /**
     * @inheritDoc
     */
    protected function jsonResponse()
    {
        return ['model' => $this->object, 'message' => $this->object::friendlyName()." has been created"];
    }

    /**
     * Notify the user
     */
    protected function notify()
    {
        \Notify::success($this->object::friendlyName().' has been created');
    }

    /**
     * Redirects the user after success
     * 
     * @return RedirectResponse
     */
    protected function redirect()
    {
        return redirect($this->object->uris()->make('index', [], $this->getRouteScope()));
    }

    /**
     * Store the model
     * 
     * @param array  $validated
     */
    protected function performStore(array $validated)
    {
        $this->model->saveFields($validated);
    }

    /**
     * Validates a request and return validated array
     * 
     * @return array
     */
    protected function validateRequest(): array
    {
        return FieldValidator::forContext($this->object, $this)->validateRequest($this->request);
    }

    /**
     * Behaviour when store fails
     * 
     * @param \Exception $exception
     * 
     * @throws \Exception
     */
    protected function onFailure(\Exception $exception)
    {
        throw $exception;
    }
}