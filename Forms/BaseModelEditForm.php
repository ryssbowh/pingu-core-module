<?php

namespace Pingu\Core\Forms;

use Illuminate\Support\Str;

class BaseModelEditForm extends BaseModelCreateForm
{
    /**
     * Method for this form, POST GET DELETE PATCH and PUT are valid
     * 
     * @return string
     */
    public function method(): string
    {
        return 'PUT';
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
        return 'edit-'.$this->model->identifier();
    }
}