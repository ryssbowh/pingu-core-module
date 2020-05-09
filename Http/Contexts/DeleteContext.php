<?php

namespace Pingu\Core\Http\Contexts;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Pingu\Core\Contracts\RouteContexts\RouteContextContract;
use Pingu\Core\Entities\BaseModel;
use Pingu\Core\Renderers\AdminViewRenderer;
use Pingu\Core\Support\Contexts\BaseRouteContext;
use Pingu\Forms\Support\Form;

class DeleteContext extends BaseRouteContext implements RouteContextContract
{
    /**
     * @inheritDoc
     */
    public static function scope(): string
    {
        return 'delete';
    }

    /**
     * @inheritDoc
     */
    public function getResponse(BaseModel $model = null)
    {
        if ($this->request->isMethod('delete')) {
            try {
                $this->performDelete();
            } catch(\Exception $e){
                return $this->onFailure($e);
            }
            return $this->onSuccess();
        }
        return $this->renderView();
    }

    /**
     * Perform the deletion
     */
    protected function performDelete()
    {
        $this->object->delete();
    }

    /**
     * Behaviour on deletion success
     * 
     * @return RedirectResponse
     */
    protected function onSuccess()
    {
        if ($this->wantsJson()) {
            return ['model' => $this->object, 'message' => $this->object::friendlyName()." has been deleted"];
        }
        \Notify::success($this->object::friendlyName().' has been deleted');
        return redirect($this->object::uris()->make('index', [], $this->getRouteScope()));
    }

    /**
     * Renders a delete confirm form
     * 
     * @return string
     */
    protected function renderView(): string
    {
        \ContextualLinks::addObjectActions($this->object, $this->getRouteScope());
        $form = $this->getForm($this->getFormAction());
        $with = [
            'form' => $form,
            'model' => $this->object,
            'indexUrl' => $this->object::uris()->make('delete', $this->object, $this->getRouteScope())
        ];
        $renderer = new AdminViewRenderer($this->getViewNames(), 'delete-'.$this->object->identifier(), $with);
        return $renderer->render();
    }

    /**
     * View names for deleting models
     * 
     * @return array
     */
    protected function getViewNames(): array
    {
        return ['pages.entities.'.$this->object->identifier().'.delete', 'pages.entities.delete'];
    }

    /**
     * Action for the form
     * 
     * @return array
     */
    protected function getFormAction(): array
    {
        return ['url' => $this->object->uris()->make('delete', $this->object, $this->getRouteScope())];
    }

    /**
     * Create the delete form
     * 
     * @param array $action
     * 
     * @return Form
     */
    protected function getForm(array $action): Form
    {   
        return $this->object->forms()->delete([$this->object, $action]);
    }

    /**
     * Behaviour when delete fails
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