<?php

namespace Pingu\Core\Http\Contexts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Pingu\Core\Contracts\RouteContexts\RouteContextContract;
use Pingu\Core\Entities\BaseModel;
use Pingu\Core\Renderers\AdminViewRenderer;
use Pingu\Core\Support\Contexts\BaseRouteContext;
use Pingu\Forms\Support\Form;

class IndexContext extends BaseRouteContext implements RouteContextContract
{
    /**
     * @inheritDoc
     */
    public static function scope(): string
    {
        return 'index';
    }

    /**
     * @inheritDoc
     */
    public function getResponse()
    {
        $filters = $this->getFilters();
        $pageSize = $this->getPageSize();
        $sortField = $this->getSortField();
        $sortOrder = $this->getSortOrder();
        $filterable = $this->getFilterableFields();

        $query = $this->object->select($this->object->getTable().'.*');
        foreach ($filters as $fieldName => $value) {
            if (!in_array($fieldName, $filterable)) {
                continue;
            }
            if (!is_null($value)) {
                $this->object->fieldRepository()->get($fieldName)->filterQueryModifier($query, $value, $this->object);
            }
        }

        $query->orderBy($this->object->getTable().'.'.$sortField, $sortOrder);
        $models = $query->paginate($pageSize, ['*'], 'page');
        $models->appends($this->request->input());

        if ($this->wantsJson()) {
            return $this->jsonResponse($models);
        }
        return $this->renderView($models);
    }

    /**
     * Fields to filter on
     * 
     * @return array
     */
    protected function getFilterableFields(): array
    {
        return $this->object->getFilterable();
    }

    /**
     * Get filters
     * 
     * @return array
     */
    protected function getFilters(): array
    {
        return $this->request->input('filters', []);
    }

    /**
     * Page size
     * 
     * @return int
     */
    protected function getPageSize(): int
    {
        return (int)$this->request->input('pageSize', $this->object->getPerPage());
    }

    /**
     * Sort field
     * 
     * @return srting
     */
    protected function getSortField(): string
    {
        return $this->request->input('sortField', $this->object->getKeyName());
    }

    /**
     * Sort order
     * 
     * @return string
     */
    protected function getSortOrder(): string
    {
        return $this->request->input('sortOrder', 'asc');
    }

    /**
     * Returns a json valid response
     * 
     * @param Form $form
     * 
     * @return array
     */
    protected function jsonResponse(LengthAwarePaginator $models): array
    {
        return ['models' => $object->toArray(), 'total' => $models->count()];
    }

    /**
     * Renders the view for an index request
     * 
     * @param LengthAwarePaginator $models
     *
     * @return string
     */
    protected function renderView(LengthAwarePaginator $models): string
    {
        $renderer = new AdminViewRenderer($this->getViewNames(), 'create-'.$this->object->identifier(), $this->getViewData($models));
        return $renderer->render();
    }

    /**
     * Data for the view
     * 
     * @param Form $form
     * 
     * @return array
     */
    protected function getViewData(LengthAwarePaginator $models): array
    {
        return [
            'models' => $models,
            'model' => $this->object,
            'createUrl' => $this->object::uris()->make('create', [], $this->getRouteScope()),
            'filterForm' => $this->getFilterForm()
        ];
    }

    /**
     * Get the form to filter a model
     *
     * @return Form
     */
    protected function getFilterForm(): Form
    {
        $indexUrl = $this->object::uris()->make('index', [], $this->getRouteScope());
        return $this->object->forms()->filter([$this->object, $this->getFilterableFields(), ['url' => $indexUrl]]);
    }

    /**
     * View names for creating models
     * 
     * @return array
     */
    protected function getViewNames(): array
    {
        return ['pages.entities.'.class_machine_name($this->object).'.index', 'pages.entities.index'];
    }
}