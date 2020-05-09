<?php

namespace Pingu\Core\Http\Contexts;

use Illuminate\Support\Collection;
use Pingu\Core\Contracts\RouteContexts\RouteContextContract;
use Pingu\Core\Entities\BaseModel;
use Pingu\Core\Renderers\AdminViewRenderer;
use Pingu\Core\Support\Contexts\BaseRouteContext;
use Pingu\Forms\Support\Form;

class EditContext extends BaseRouteContext implements RouteContextContract
{
    /**
     * @inheritDoc
     */
    public static function scope(): string
    {
        return 'edit';
    }

    /**
     * @inheritDoc
     */
    public function getResponse(BaseModel $model = null)
    {
        $form = $this->getForm();
        if ($this->wantsJson()) {
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
        $form->option('title', 'Edit '.$this->object::friendlyName());
        return ['html' => $form->render()];
    }

    /**
     * Get fields for the edit form
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
        return ['url' => $this->object->uris()->make('update', $this->object, $this->getRouteScope())];
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
        return $this->object->forms()->edit([$this->object, $this->getFormAction(), $this->getFields()]);
    }

    /**
     * Renders the view for an edit request
     * 
     * @param Form   $form
     *
     * @return string
     */
    protected function renderView(Form $form): string
    {
        \ContextualLinks::addObjectActions($this->object, $this->getRouteScope());
        
        $renderer = new AdminViewRenderer($this->getViewNames(), 'edit-'.$this->object->identifier(), $this->getViewData($form));
        return $renderer->render();
    }

    /**
     * Data for the view
     * 
     * @param Form $form
     * 
     * @return array
     */
    protected function getViewData(Form $form): array
    {
        return [
            'form' => $form,
            'model' => $this->object,
            'indexUrl' => $this->object::uris()->make('index', $this->object, $this->getRouteScope())
        ];
    }

    /**
     * View names for editing models
     * 
     * @return array
     */
    protected function getViewNames(): array
    {
        return ['pages.entities.'.class_machine_name($this->object).'.edit', 'pages.entities.edit'];
    }
}