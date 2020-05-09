<?php

namespace Pingu\Core\Forms;

use Illuminate\Support\Collection;
use Pingu\Core\Entities\BaseModel;
use Pingu\Forms\Support\Fields\Submit;
use Pingu\Forms\Support\Form;

class BaseModelCreateForm extends Form
{
    protected $model;
    protected $action;
    protected $fields;

    public function __construct(BaseModel $model, array $action, Collection $fields)
    {
        $this->model = $model;
        $this->action = $action;
        $this->fields = $fields;
        parent::__construct();
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
    public function method(): string
    {
        return 'POST';
    }

    /**
     * @inheritDoc
     */
    public function elements(): array
    {
        $model = $this->model;
        $fields = $this->fields->map(function ($field) use ($model) {
            $value = $field->formValue($model);
            return $field->toFormElement($value);
        })->all();
        $fields[] = new Submit('_submit');
        return $fields;
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return 'create-'.$this->model->identifier();
    }
}