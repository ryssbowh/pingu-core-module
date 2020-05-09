<?php

namespace Pingu\Core\Http\Contexts;

use Pingu\Core\Contracts\RouteContexts\ValidatableContextContract;
use Pingu\Core\Entities\BaseModel;
use Pingu\Core\Support\Contexts\BaseRouteContext;
use Pingu\Field\Contracts\HasFieldsContract;
use Pingu\Field\Support\FieldValidator;

class UpdateContext extends BaseRouteContext implements ValidatableContextContract
{
    /**
     * @inheritDoc
     */
    public static function scope(): string
    {
        return 'update';
    }

    /**
     * @inheritDoc
     */
    public function getResponse()
    {
        try{
            $validated = $this->validateRequest();
            $this->performUpdate($validated);
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
        return ['model' => $this->object, 'message' => $this->object::friendlyName()." has been updated"];
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
     * Notifies the user
     */
    protected function notify()
    {
        if (!$this->object->wasChanged()) {
            \Notify::info('No changes made to '.$this->object::friendlyName());
        } else {
            \Notify::success($this->object::friendlyName().' has been saved');
        }
    }

    /**
     * Updates the model
     * 
     * @param array  $validated
     */
    protected function performUpdate(array $validated)
    {
        $this->object->saveFields($validated);
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
     * Behaviour when update fails
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