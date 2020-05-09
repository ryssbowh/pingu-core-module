<?php

namespace Pingu\Core\Forms;

use Pingu\Core\Contracts\RendererContract;
use Pingu\Field\Contracts\HasFieldsContract;
use Pingu\Forms\Renderers\FilterFormRenderer;
use Pingu\Forms\Support\Fields\Link;
use Pingu\Forms\Support\Fields\Submit;
use Pingu\Forms\Support\Form;

class BaseModelFilterForm extends Form
{
   
    /**
     * @var Entity
     */
    protected $model;

    /**
     * @var array
     */
    protected $action;

    /**
     * @inheritDoc
     */
    public function __construct(HasFieldsContract $model, array $fields, array $action)
    {
        $this->model = $model;
        $this->action = $action;
        $this->fields = $fields;
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    public function elements(): array
    {
        $fields = $this->model->fieldRepository()->only($this->fields)->map(function ($field) {
            $field = $field->toFilterFormElement();
            $field->option('showLabel', false);
            $field->option('placeholder', $field->option('label'));
            return $field;
        })->all();
        $fields[] = new Submit(
            '_submit', 
            [
                'label' => 'Filter'
            ]
        );
        $fields[] = new Link(
            '_reset', 
            [
                'label' => 'Reset',
                'url' => $this->action['url']
            ]
        );
        return $fields;
    }

    /**
     * @inheritDoc
     */
    public function method(): string
    {
        return 'GET';
    }

    /**
     * @inheritDoc
     */
    public function action(): array
    {
        return $this->action;
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return 'filter-'.$this->model->identifier();
    }

    /**
     * @inheritDoc
     */
    public function getRenderer(): RendererContract
    {
        return new FilterFormRenderer($this);
    }
}