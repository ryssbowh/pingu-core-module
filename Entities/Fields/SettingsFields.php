<?php

namespace Pingu\Core\Entities\Fields;

use Pingu\Field\Support\FieldRepository\BaseFieldRepository;

class SettingsFields extends BaseFieldRepository
{
    public function fields(): array
    {
        return [];
    }

    public function rules(): array
    {
        return [];
    }

    public function messages(): array
    {
        return [];
    }
}