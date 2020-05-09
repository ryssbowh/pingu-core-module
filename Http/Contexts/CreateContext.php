<?php

namespace Pingu\Core\Http\Contexts;

use Illuminate\Support\Collection;
use Pingu\Core\Contracts\RouteContexts\RouteContextContract;
use Pingu\Core\Entities\BaseModel;
use Pingu\Core\Renderers\AdminViewRenderer;
use Pingu\Core\Support\Contexts\BaseRouteContext;
use Pingu\Forms\Support\Form;

class CreateContext extends BaseRouteContext implements RouteContextContract
{
    /**
     * @inheritDoc
     */
    public static function scope(): string
    {
        return 'create';
    }

    /**
     * @inheritDoc
     */
    public function getResponse()
    {
        $form = $this->getForm();
        if ($this->request->expectsJson()) {
            return $this->jsonResponse($form);
        }
        return $this->renderView($form);
    }

    /**
     * Returns a json valid response
     * 
     * @param Form $form
     * 
     * @return array
     */
    protected function jsonResponse(Form $form): array
    {
        $form->option('title', 'Create a '.$this->object::friendlyName());
        return ['html' => $form->render()];
    }

    /**
     * Get fields for the create form
     * 
     * @return Collection
     */
    protected function getFields(): Collection
    {
        return $this->object->fieldRepository()->all();
    }

    /**
     * Action for the form
     * 
     * @return array
     */
    protected function getFormAction(): array
    {
        return ['url' => $this->object->uris()->make('store', $this->object, $this->getRouteScope())];
    }

    /**
     * Create the create form
     * 
     * @param array  $action
     * 
     * @return Form
     */
    protected function getForm(): Form
    {   
        return $this->object->forms()->create([$this->object, $this->getFormAction(), $this->getFields()]);
    }

    /**
     * Get the view for a create request
     * 
     * @param Form   $form
     *
     * @return string
     */
    protected function renderView(Form $form): string
    {
        $with = [
            'form' => $form,
            'model' => $this->object,
            'indexUrl' => $this->object::uris()->make('index', $this->object, $this->getRouteScope())
        ];
        $renderer = new AdminViewRenderer($this->getViewNames(), 'create-'.$this->object->identifier(), $with);
        return $renderer->render();
    }

    /**
     * View names for creating models
     * 
     * @return array
     */
    protected function getViewNames(): array
    {
        return ['pages.entities.'.$this->object->identifier().'.create', 'pages.entities.create'];
    }
}