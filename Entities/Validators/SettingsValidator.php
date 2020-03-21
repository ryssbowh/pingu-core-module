<?php

namespace Pingu\Core\Entities\Validators;

use Pingu\Field\Support\FieldValidator\BaseFieldsValidator;

class SettingsValidator extends BaseFieldsValidator
{
    public function rules(bool $updating): array
    {
        return [];
    }

    public function messages(): array
    {
        return [];
    }
}