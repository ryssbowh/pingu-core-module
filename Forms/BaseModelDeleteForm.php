<?php

namespace Pingu\Core\Forms;

use Illuminate\Support\Str;
use Pingu\Core\Entities\BaseModel;
use Pingu\Forms\Support\Fields\Submit;
use Pingu\Forms\Support\Form;

class BaseModelDeleteForm extends Form
{
    protected $model;
    protected $action;

    public function __construct(BaseModel $model, array $action)
    {
        $this->model = $model;
        $this->action = $action;
        parent::__construct();
    }
    /**
     * Fields definitions for this form, classes used here
     * must extend Pingu\Forms\Support\Field
     * 
     * @return array
     */
    public function elements(): array
    {
        return [new Submit('_submit')];
    }

    /**
     * Method for this form, POST GET DELETE PATCH and PUT are valid
     * 
     * @return string
     */
    public function method(): string
    {
        return 'DELETE';
    }

    /**
     * @inheritDoc
     */
    public function action(): array
    {
        return $this->action;
    }

    /**
     * Name for this form, ideally it would be application unique, 
     * best to prefix it with the name of the module it's for.
     * only alphanumeric and hyphens
     * 
     * @return string
     */
    public function name(): string
    {
        return 'delete-'.$this->model->identifier();
    }
}