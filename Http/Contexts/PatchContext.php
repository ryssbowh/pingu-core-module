<?php

namespace Pingu\Core\Http\Contexts;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Pingu\Core\Contracts\RouteContexts\ValidatableContextContract;
use Pingu\Core\Entities\BaseModel;
use Pingu\Core\Support\Contexts\BaseRouteContext;
use Pingu\Field\Contracts\HasFieldsContract;
use Pingu\Field\Support\FieldValidator;

class PatchContext extends BaseRouteContext implements ValidatableContextContract
{
    /**
     * @inheritDoc
     */
    public static function scope(): string
    {
        return 'patch';
    }

    /**
     * @inheritDoc
     */
    public function getResponse()
    {
        $post = $this->request->post();
        if (!isset($post['models'])) {
            return $this->onPatchFailure(ValidationException::withMessages(['*' => "'models' must be set for a patch request"]));
        }

        $patched = $this->performPatch($post['models']);

        if ($this->wantsJson()) {
            return $this->jsonResponse($patched);
        }
        return $this->redirect($patched);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRules(HasFieldsContract $model): array
    {
        return $model->fieldRepository()->validationRules()->map(
            function ($rule) {
                return 'sometimes|'.$rule;
            }
        )->toArray();
    }

    /**
     * @inheritDoc
     */
    public function getValidationMessages(HasFieldsContract $model): array
    {
        return $model->fieldRepository()->validationMessages()->toArray();
    }

    /**
     * Perform patch
     * 
     * @param array $data
     * 
     * @return Collection
     */
    protected function performPatch(array $data): Collection
    {
        $toPatch = [];
        $patched = collect();
        try {
            foreach ($data as $line) {
                $toPatch[] = $this->validateItem($line);
            }
            foreach ($toPatch as $data) {
                $data[0]->saveFields($data[1]);
                $patched->push($data[0]);
            }
        } catch (\Exception $e) {
            return $this->onFailure($e);
        }
        return $patched;
    }

    /**
     * Validate a single item
     * 
     * @param array  $data
     * 
     * @return array
     */
    protected function validateItem(array $data): array
    {
        $key = $this->object->getKeyName();
        if (!isset($data[$key])) {
            throw ValidationException::withMessages([$key => "'$key' must be set for each line of a patch request"]);
        }
        $item = $this->object::find($data[$key]);
        if (!$item) {
            throw ValidationException::withMessages([$key => "Can't find a ".get_class($this->object)." with key $key"]);
        }
        unset($data[$key]);
        return [$item, FieldValidator::forContext($item, $this)->validateData($data)];
    }

    /**
     * Returns json response after successful patch
     * 
     * @param Collection $patched
     * 
     * @return array
     */
    protected function jsonResponse(Collection $patched): array
    {
        return ['models' => $patched->toArray(), 'message' => $this->successMessage()];
    }

    /**
     * Redirects after successful patch
     * 
     * @param Collection $patched
     * 
     * @return RedirectResponse
     */
    protected function redirect(Collection $patched)
    {
        \Notify::success($this->successMessage());
        return redirect($this->object::uris()->make('index', [], $this->getRouteScope()));
    }

    /**
     * Success message
     * 
     * @return string
     */
    protected function successMessage(): string
    {
        return $this->object::friendlyNames().' have been saved';
    }

    /**
     * Returns reponse after failed patch
     * 
     * @param \Exception $e
     */
    protected function onFailure(\Exception $e)
    {
        throw $e;
    }
}