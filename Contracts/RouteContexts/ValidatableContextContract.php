<?php

namespace Pingu\Core\Contracts\RouteContexts;

use Illuminate\Support\Collection;
use Pingu\Field\Contracts\HasFieldsContract;

interface ValidatableContextContract extends RouteContextContract
{
    /**
     * Validation rules getter
     * 
     * @return array
     */
    public function getValidationRules(HasFieldsContract $model): array;

    /**
     * Validation messages getter
     * 
     * @return array
     */
    public function getValidationMessages(HasFieldsContract $model): array;
}